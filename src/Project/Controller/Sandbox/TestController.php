<?php

declare(strict_types=1);

namespace Project\Controller\Sandbox;

use DateTimeImmutable;
use Override;
use Project\Contract\Controller\SandboxControllerInterface;
use Project\View\Sandbox\TestView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\View\Contract\ViewContainerInterface;

final class TestController extends AbstractSandboxController implements SandboxControllerInterface
{
    #[Override]
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
                $this->getLocalizationContainer($request),
                17,
                $this->applicationDependencyContainer->getRequestServiceContainer()
                    ->getServerRequestAttributeService()->getRoutePart(3, $request),
                // A system time: the formatter shows it in the display time zone.
                new DateTimeImmutable(),
                1234567.891,
                // Plural examples: English and German have `one` (1) and `other` (5).
                [1, 5],
            ),
            'sandbox/test',
        );
    }
}
