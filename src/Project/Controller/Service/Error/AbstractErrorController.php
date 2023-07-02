<?php

declare(strict_types=1);

namespace Project\Controller\Service\Error;

use Project\Controller\Service\AbstractController;
use Project\View\Error\MainView;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * An abstract controller with dependencies specific to current module.
 */
abstract class AbstractErrorController extends AbstractController
{
    protected function createMainViewContainer(ViewContainerInterface $viewContainer): ViewContainerInterface
    {
        return $this->viewContainerFactory->createViewContainerFromView(
            new MainView(
                // baseUrl; idea: set this dynamically as a route attribute by using a middleware.
                $this->configurationGetter->getString('BASE_URL'),
                // data
                $this->viewRenderer->render($viewContainer),
            ),
            // Set main template to use (can be customized - eg. different "theme" - based on user preference).
            'main/main.error.default',
        );
    }
}
