<?php

declare(strict_types=1);

namespace Project\Controller\API;

use Project\Contract\Container\API\APILocalServiceContainerInterface;
use Project\Controller\AbstractController;
use Project\Middleware\API\ApiAuthenticationMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * An abstract controller with dependencies specific to current module.
 */
abstract class AbstractAPIController extends AbstractController
{
    abstract protected function createViewContainer(
        ServerRequestInterface $request,
        string $userId,
    ): ViewContainerInterface;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data processing would go here (use services).

        $userId = $this->getUserIdFromRequest($request);
        /**
         * Sanity check, should never arrive here;
         * userId is set by ApiAuthenticationMiddleware, and we do not arrive here if not set (not authenticated)
         */
        if ($userId === null) {
            throw new UnexpectedValueException('User id not set.');
        }

        // Create view.
        $viewContainer = $this->createViewContainer($request, $userId);

        // Return response.
        return $this->createResponse($request, $viewContainer);
    }

    protected function createMainViewContainer(
        ServerRequestInterface $request,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        return $this->createMainViewContainerWithTemplate($request, 'main/main.api.default', $viewContainer);
    }

    protected function getApiVersionString(): string
    {
        return $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
        ->getString('API_VERSION');
    }

    protected function getCurrentRoute(ServerRequestInterface $request): ?string
    {
        return $this->applicationDependencyContainer->getDataExtractionContainer()
            ->getStrictDataExtractionService()->getNullableString(
                $request->getAttribute(RoutePartsInterface::ROUTE_PART_2, null),
            );
    }

    /**
     * Return local implementation of LocalDependencyContainerInterface
     */
    protected function getLocalDependencyContainer(): APILocalServiceContainerInterface
    {
        if (!$this->localDependencyContainer instanceof APILocalServiceContainerInterface) {
            throw new UnexpectedValueException('Invalid instance.');
        }

        return $this->localDependencyContainer;
    }

    protected function getUserIdFromRequest(ServerRequestInterface $request): ?string
    {
        return $this->applicationDependencyContainer->getDataExtractionContainer()
            ->getStrictDataExtractionService()->getNullableString(
                $request->getAttribute(ApiAuthenticationMiddleware::REQUEST_ATTRIBUTE_KEY_USER_ID, null),
            );
    }
}
