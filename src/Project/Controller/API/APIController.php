<?php

declare(strict_types=1);

namespace Project\Controller\API;

use Error;
use Fig\Http\Message\RequestMethodInterface;
use JsonException;
use OutOfBoundsException;
use OutOfRangeException;
use Project\Contract\Controller\APIControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\JSONAPI\DataTransfer\Document\JSONAPI;
use WebServCo\JSONAPI\DataTransfer\Example\ExampleAttributes;
use WebServCo\JSONAPI\DataTransfer\Example\ExampleData;
use WebServCo\JSONAPI\DataTransfer\Example\ExampleDataItemMeta;
use WebServCo\JSONAPI\DataTransfer\Example\ExampleDocumentMeta;
use WebServCo\JSONAPI\View\ItemView;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * A general API Controller.
 *
 * @todo solve CouplingBetweenObjects
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class APIController extends AbstractAPIController implements APIControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data processing would go here (use services).

        $userId = $this->getUserIdFromRequest($request);

        // Create JSONAPI handler.
        $jsonApiHandler = $this->getLocalDependencyContainer()->getJsonApiServiceContainer()
            ->getDefaultHandlerFactory()->createHandler(
                [
                    RequestMethodInterface::METHOD_GET,
                ],
            );

        try {
            // Handle request.
            $jsonApiHandler->handleRequest($request);

            // Handle valid request.
            if ($jsonApiHandler->isSent() && $jsonApiHandler->isValid()) {
                // Create view.
                $viewContainer = $this->createViewContainer($request, $userId);

                // Return response.
                return $this->createResponse($request, $viewContainer);
            }

            // If we arrive here, there is a problem.
            throw new UnexpectedValueException('Invalid request.');
        } catch (JsonException | OutOfBoundsException | OutOfRangeException | UnexpectedValueException $e) {
            $responseCode = $this->getResponseCodeBasedOnThrowable($e);

            // Error processing request body.
            $jsonApiHandler->addError(new Error($e->getMessage(), $responseCode));

            // Create error view container.
            $viewContainer = $this->createErrorViewContainer($jsonApiHandler);

            // Return response.
            return $this->createResponse($request, $viewContainer, $responseCode);
        }
    }

    private function createItemView(ServerRequestInterface $request, string $userId): ItemView
    {
        return new ItemView(
            new JSONAPI('1.1'),
            new ExampleData(
                1,
                new ExampleAttributes(
                    $this->applicationDependencyContainer->getRequestServiceContainer()
                        ->getServerRequestAttributeService()->getRoutePart(3, $request),
                    $userId,
                ),
                new ExampleDataItemMeta('dataValue'),
            ),
            null,
            new ExampleDocumentMeta(
                $this->getCurrentRoute($request),
                $this->getApiVersionString(),
            ),
        );
    }

    private function createViewContainer(ServerRequestInterface $request, string $userId): ViewContainerInterface
    {
        /**
         * Do not simply assume a JSONRendererInterface will be used / enforced.
         * Set a fallback template (could contain for example a general message).
         */
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            $this->createItemView($request, $userId),
            'api/default',
        );
    }
}
