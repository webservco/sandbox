<?php

declare(strict_types=1);

namespace Project\Controller\Sandbox;

use Project\Controller\AbstractController;
use Project\View\Sandbox\MainView;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * An abstract controller with dependencies specific to current module.
 */
abstract class AbstractSandboxController extends AbstractController
{
    protected function createMainViewContainer(ViewContainerInterface $viewContainer): ViewContainerInterface
    {
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new MainView(
                // baseUrl; idea: set this dynamically as a route attribute by using a middleware.
                $this->getConfigurationGetter()->getString(
                    'BASE_URL',
                ),
                // data
                $this->viewServicesContainer->getViewRenderer()->render($viewContainer),
            ),
            // Set main template to use (can be customized - eg. different "theme" - based on user preference).
            'main/main.sandbox.default',
        );
    }
}
