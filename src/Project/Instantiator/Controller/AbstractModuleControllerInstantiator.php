<?php

declare(strict_types=1);

namespace Project\Instantiator\Controller;

use LogicException;
use OutOfRangeException;
use WebServCo\Controller\Contract\ControllerInterface;
use WebServCo\Controller\Contract\ModuleControllerInstantiatorInterface;
use WebServCo\DependencyContainer\Contract\FactoryContainerInterface;
use WebServCo\DependencyContainer\Contract\ServiceContainerInterface;
use WebServCo\View\Contract\ViewContainerFactoryInterface;
use WebServCo\View\Contract\ViewRendererInterface;

use function class_exists;

abstract class AbstractModuleControllerInstantiator implements ModuleControllerInstantiatorInterface
{
    public function __construct(
        protected FactoryContainerInterface $factoryContainer,
        protected ServiceContainerInterface $serviceContainer,
    ) {
    }

    public function instantiateSpecificModuleController(
        string $controllerClassName,
        ViewContainerFactoryInterface $viewContainerFactory,
        ViewRendererInterface $viewRenderer,
    ): ControllerInterface {
        /**
         * Class existence already checked elsewhere, here again for static analisys.
         */
        if (!class_exists($controllerClassName, true)) {
            throw new OutOfRangeException('Controller class does not exist.');
        }

        /**
         * Psalm error: "Cannot call constructor on an unknown class".
         *
         * @psalm-suppress MixedMethodCall
         */
        $object = new $controllerClassName(
            // Object: \WebServCo\Configuration\Contract\ConfigurationGetterInterface
            $this->serviceContainer->getConfigurationGetter(),
            // Object: \WebServCo\Stopwatch\Contract\LapTimerInterface
            $this->serviceContainer->getLapTimer(),
            // Object: \Psr\Log\LoggerInterface
            $this->serviceContainer->getLogger($controllerClassName),
            // Object: \Psr\Http\Message\ResponseFactoryInterface
            $this->factoryContainer->getResponseFactory(),
            // Object: \Psr\Http\Message\StreamFactoryInterface
            $this->factoryContainer->getStreamFactory(),
            // Object: \WebServCo\View\Contract\ViewContainerFactoryInterface
            $viewContainerFactory,
            // Object: \WebServCo\View\Contract\ViewRendererInterface
            $viewRenderer,
        );

        if (!$object instanceof ControllerInterface) {
            throw new LogicException('Object is not an instance of the required interface.');
        }

        return $object;
    }
}
