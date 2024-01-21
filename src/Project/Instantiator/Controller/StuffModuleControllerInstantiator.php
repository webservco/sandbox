<?php

declare(strict_types=1);

namespace Project\Instantiator\Controller;

use LogicException;
use Project\Contract\Controller\StuffControllerInterface;
use Project\Factory\Container\StuffLocalDependencyContainerFactory;
use WebServCo\Controller\Contract\ControllerInterface;
use WebServCo\Controller\Contract\ModuleControllerInstantiatorInterface;
use WebServCo\Controller\Service\AbstractModuleControllerInstantiator;
use WebServCo\DependencyContainer\Contract\ApplicationDependencyContainerInterface;
use WebServCo\Reflection\Contract\ReflectionServiceInterface;
use WebServCo\View\Contract\ViewServicesContainerInterface;

final class StuffModuleControllerInstantiator extends AbstractModuleControllerInstantiator implements
    ModuleControllerInstantiatorInterface
{
    public function instantiateModuleController(
        ApplicationDependencyContainerInterface $applicationDependencyContainer,
        string $controllerClassName,
        ReflectionServiceInterface $reflectionService,
        ViewServicesContainerInterface $viewServicesContainer,
    ): ControllerInterface {
        $localDependencyContainerFactory = new StuffLocalDependencyContainerFactory($applicationDependencyContainer);

        $object = $this->instantiateModuleControllerWithLocalDependencyContainer(
            $applicationDependencyContainer,
            $controllerClassName,
            $localDependencyContainerFactory->createLocalDependencyContainer(),
            $reflectionService,
            $viewServicesContainer,
        );

        if (!$object instanceof StuffControllerInterface) {
            throw new LogicException('Object is not an instance of the required interface.');
        }

        return $object;
    }
}
