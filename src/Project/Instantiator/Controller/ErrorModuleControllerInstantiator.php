<?php

declare(strict_types=1);

namespace Project\Instantiator\Controller;

use LogicException;
use Project\Controller\Contract\ErrorControllerInterface;
use WebServCo\Controller\Contract\ControllerInterface;
use WebServCo\Controller\Contract\ModuleControllerInstantiatorInterface;
use WebServCo\View\Contract\ViewContainerFactoryInterface;
use WebServCo\View\Contract\ViewRendererInterface;

final class ErrorModuleControllerInstantiator extends AbstractModuleControllerInstantiator implements
    ModuleControllerInstantiatorInterface
{
    public function instantiateSpecificModuleController(
        string $controllerClassName,
        ViewContainerFactoryInterface $viewContainerFactory,
        ViewRendererInterface $viewRenderer,
    ): ControllerInterface {
        $object = parent::instantiateSpecificModuleController(
            $controllerClassName,
            $viewContainerFactory,
            $viewRenderer,
        );

        if (!$object instanceof ErrorControllerInterface) {
            throw new LogicException('Object is not an instance of the required interface.');
        }

        return $object;
    }
}
