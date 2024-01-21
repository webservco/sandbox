<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Project\Contract\Container\Stuff\StuffLocalServiceContainerInterface;
use Project\Controller\AbstractController;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * An abstract controller with dependencies specific to current module.
 */
abstract class AbstractStuffController extends AbstractController
{
    protected function createMainViewContainer(
        ServerRequestInterface $request,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        return $this->createMainViewContainerWithTemplate($request, 'main/main.stuff.pico', $viewContainer);
    }

    /**
     * Return local implementation of LocalDependencyContainerInterface
     */
    protected function getLocalDependencyContainer(): StuffLocalServiceContainerInterface
    {
        if (!$this->localDependencyContainer instanceof StuffLocalServiceContainerInterface) {
            throw new UnexpectedValueException('Invalid instance.');
        }

        return $this->localDependencyContainer;
    }
}
