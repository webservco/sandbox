<?php

declare(strict_types=1);

namespace Project\Instantiator\Controller;

use LogicException;
use Project\Controller\Contract\SandboxControllerInterface;
use WebServCo\Controller\Contract\ControllerInterface;
use WebServCo\Controller\Contract\ModuleControllerInstantiatorInterface;
use WebServCo\View\Contract\ViewContainerFactoryInterface;
use WebServCo\View\Contract\ViewRendererInterface;

final class SandboxModuleControllerInstantiator extends AbstractModuleControllerInstantiator implements
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

        if (!$object instanceof SandboxControllerInterface) {
            throw new LogicException('Object is not an instance of the required interface.');
        }

        return $object;
    }
}
