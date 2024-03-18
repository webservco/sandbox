<?php

declare(strict_types=1);

namespace Project\Controller\API;

use Project\Controller\AbstractController;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * An abstract controller with dependencies specific to current module.
 */
abstract class AbstractAPIController extends AbstractController
{
    protected function createMainViewContainer(
        ServerRequestInterface $request,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        return $this->createMainViewContainerWithTemplate($request, 'main/main.api.default', $viewContainer);
    }

    protected function getApiVersionString(): string
    {
        return $this->getConfigurationGetter()->getString('API_VERSION');
    }

    protected function getCurrentRoute(ServerRequestInterface $request): ?string
    {
        return $this->applicationDependencyContainer->getDataExtractionContainer()
            ->getStrictDataExtractionService()->getNullableString(
                $request->getAttribute(RoutePartsInterface::ROUTE_PART_2, null),
            );
    }
}
