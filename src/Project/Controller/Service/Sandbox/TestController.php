<?php

declare(strict_types=1);

namespace Project\Controller\Service\Sandbox;

use Project\Controller\Contract\SandboxControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;

final class TestController extends AbstractSandboxController implements SandboxControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data processing would go here (use services).

        // Gathered after data processing.
        $data = [
            'intProperty' => 17,
            'stringProperty' => $request->getAttribute(RoutePartsInterface::ROUTE_PART_3, 'default value'),
        ];

        // Log.
        $this->logger->debug('data debug (see context)', $data);

        /**
         * Create view.
         *
         * Note: ViewContainerFactoryInterface sets templateName with a sensible default.
         * Here we could call setTemplateName path to set for example a different template in a certain situation.
         */
        $viewContainer = $this->viewContainerFactory->createViewContainerFromData($data);

        // Return response.
        return $this->createResponse($viewContainer);
    }
}