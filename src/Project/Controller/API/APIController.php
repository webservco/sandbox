<?php

declare(strict_types=1);

namespace Project\Controller\API;

use Project\Contract\Controller\APIControllerInterface;
use Project\DataTransfer\API\Example\ExampleAttributes;
use Project\DataTransfer\API\Example\ExampleData;
use Project\DataTransfer\API\Example\ExampleDataItemMeta;
use Project\DataTransfer\API\Example\ExampleDocumentMeta;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\JSONAPI\DataTransfer\Document\JSONAPI;
use WebServCo\JSONAPI\View\ItemView;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * A general API Controller.
 */
final class APIController extends AbstractAPIController implements APIControllerInterface
{
    protected function createViewContainer(ServerRequestInterface $request, string $userId): ViewContainerInterface
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

    private function createItemView(ServerRequestInterface $request, string $userId): ItemView
    {
        return new ItemView(
            new JSONAPI('1.1'),
            new ExampleData(
                1,
                new ExampleAttributes(
                    $this->applicationDependencyContainer->getDataExtractionContainer()
                        ->getStrictDataExtractionService()->getNullableString(
                            $request->getAttribute(RoutePartsInterface::ROUTE_PART_3, null),
                        ),
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
}
