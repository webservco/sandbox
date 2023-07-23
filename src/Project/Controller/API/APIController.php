<?php

declare(strict_types=1);

namespace Project\Controller\API;

use Project\Contract\Controller\APIControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;

/**
 * A general API Controller.
 */
final class APIController extends AbstractAPIController implements APIControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data processing would go here (use services).

        // Gathered after data processing.
        $data = [
            'result' => [
                'version' => 3,
                RoutePartsInterface::ROUTE_PART_3 => $request->getAttribute(RoutePartsInterface::ROUTE_PART_3, null),
            ],
            'route' => $request->getAttribute(RoutePartsInterface::ROUTE_PART_2, null),
        ];

        // Log.
        $this->getLogger(self::class)->debug('Data debug (see context).', $data);

        // Create view.
        $viewContainer = $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromData($data);

        // Return response.
        return $this->createResponse($viewContainer);
    }
}
