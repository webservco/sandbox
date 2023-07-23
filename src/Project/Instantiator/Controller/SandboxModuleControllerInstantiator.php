<?php

declare(strict_types=1);

namespace Project\Instantiator\Controller;

use LogicException;
use Project\Contract\Controller\SandboxControllerInterface;
use WebServCo\Controller\Contract\ControllerInterface;
use WebServCo\Controller\Contract\ModuleControllerInstantiatorInterface;
use WebServCo\Controller\Service\AbstractModuleControllerInstantiator;
use WebServCo\DependencyContainer\Contract\ApplicationDependencyContainerInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;
use WebServCo\Reflection\Contract\ReflectionServiceInterface;
use WebServCo\View\Contract\ViewServicesContainerInterface;

final class SandboxModuleControllerInstantiator extends AbstractModuleControllerInstantiator implements
    ModuleControllerInstantiatorInterface
{
    public function instantiateModuleController(
        ApplicationDependencyContainerInterface $applicationDependencyContainer,
        string $controllerClassName,
        LocalDependencyContainerInterface $localDependencyContainer,
        ReflectionServiceInterface $reflectionService,
        ViewServicesContainerInterface $viewServicesContainer,
    ): ControllerInterface {
        $object = parent::instantiateModuleController(
            $applicationDependencyContainer,
            $controllerClassName,
            $localDependencyContainer,
            $reflectionService,
            $viewServicesContainer,
        );

        if (!$object instanceof SandboxControllerInterface) {
            throw new LogicException('Object is not an instance of the required interface.');
        }

        return $object;
    }
}
