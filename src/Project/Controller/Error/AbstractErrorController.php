<?php

declare(strict_types=1);

namespace Project\Controller\Error;

use Override;
use Project\Controller\AbstractController;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * An abstract controller with dependencies specific to current module.
 */
abstract class AbstractErrorController extends AbstractController
{
    #[Override]
    protected function createMainViewContainer(
        ServerRequestInterface $request,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        return $this->createMainViewContainerWithTemplate($request, 'main/main.error.pico', $viewContainer);
    }
}
