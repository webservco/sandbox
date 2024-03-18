<?php

declare(strict_types=1);

namespace Project\Controller\API;

use Project\Contract\Controller\APIControllerInterface;
use Project\DataTransfer\API\APIResult;
use Project\View\API\APIView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * A general API Controller.
 */
final class APIController extends AbstractAPIController implements APIControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data processing would go here (use services).

        // Create view.
        $viewContainer = $this->createViewContainer($request);

        // Return response.
        return $this->createResponse($request, $viewContainer);
    }

    private function createViewContainer(ServerRequestInterface $request): ViewContainerInterface
    {
       /**
         * Do not simply assume a JSONRendererInterface will be used / enforced.
         * Set a fallback template (could contain for example a general message).
         */
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new APIView(
                new APIResult(
                    $this->applicationDependencyContainer->getDataExtractionContainer()
                    ->getStrictDataExtractionService()->getNullableString(
                        $request->getAttribute(RoutePartsInterface::ROUTE_PART_3, null),
                    ),
                ),
                $this->getCurrentRoute($request),
                $this->getApiVersionString(),
            ),
            'api/default',
        );
    }
}
