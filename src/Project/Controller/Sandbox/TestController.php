<?php

declare(strict_types=1);

namespace Project\Controller\Sandbox;

use Project\Contract\Controller\SandboxControllerInterface;
use Project\View\Sandbox\TestView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;
use WebServCo\View\Contract\ViewContainerInterface;

final class TestController extends AbstractSandboxController implements SandboxControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data processing would go here (use services).

        /**
         * Create view.
         *
         * Note: ViewContainerFactoryInterface sets templateName with a sensible default.
         * Here we could call setTemplateName path to set for example a different template in a certain situation.
         */
        $viewContainer = $this->createViewContainer($request);

        // Return response.
        return $this->createResponse($request, $viewContainer);
    }

    private function createViewContainer(ServerRequestInterface $request): ViewContainerInterface
    {
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new TestView(
                17,
                $this->applicationDependencyContainer->getDataExtractionContainer()
                    ->getStrictDataExtractionService()->getString(
                        $request->getAttribute(RoutePartsInterface::ROUTE_PART_3, 'default_value'),
                    ),
            ),
            'sandbox/test',
        );
    }
}
