<?php

declare(strict_types=1);

namespace Project\Factory\Application;

use Project\Factory\Http\RequestHandlerFactory;
use Project\Instantiator\Controller\SpecificModuleControllerInstantiator;
use WebServCo\Application\Factory\ApplicationRunnerFactory;
use WebServCo\Application\Factory\DefaultServerApplicationFactory;
use WebServCo\Controller\Factory\ControllerInstantiatorFactory;
use WebServCo\DependencyContainer\Contract\ApplicationDependencyContainerInterface;
use WebServCo\Error\Factory\DefaultErrorHandlingServiceFactory;
use WebServCo\Exception\Contract\ExceptionHandlerInterface;
use WebServCo\Exception\Factory\DefaultExceptionHandlerFactory;
use WebServCo\Http\Factory\Message\Request\Server\ServerRequestFromServerDataFactory;
use WebServCo\View\Service\ViewRendererResolver;

final class ApplicationFactoryFactory
{
    public function __construct(private ApplicationDependencyContainerInterface $applicationDependencyContainer)
    {
    }

    public function createServerApplicationFactory(): DefaultServerApplicationFactory
    {
        return new DefaultServerApplicationFactory(
            $this->createApplicationRunnerFactory(),
            new DefaultErrorHandlingServiceFactory(),
            $this->applicationDependencyContainer->getServiceContainer(),
            new ServerRequestFromServerDataFactory(),
        );
    }

    private function createApplicationRunnerFactory(): ApplicationRunnerFactory
    {
        $controllerInstantiatorFactory = $this->createControllerInstantiatorFactory();

        return new ApplicationRunnerFactory(
            new RequestHandlerFactory(
                $this->applicationDependencyContainer,
                $controllerInstantiatorFactory->createControllerInstantiator(
                    $this->applicationDependencyContainer,
                    new SpecificModuleControllerInstantiator(),
                ),
                $this->createExceptionHandler(),
                // The absolute project path, with the trailing separator.
                $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()->getString(
                    'PROJECT_PATH',
                ),
                new ViewRendererResolver(),
            ),
        );
    }

    private function createControllerInstantiatorFactory(): ControllerInstantiatorFactory
    {
        return new ControllerInstantiatorFactory();
    }

    /**
     * The same logger as the uncaught exception handler that `DefaultApplicationInitializer` sets.
     */
    private function createExceptionHandler(): ExceptionHandlerInterface
    {
        $exceptionHandlerFactory = new DefaultExceptionHandlerFactory();

        return $exceptionHandlerFactory->createExceptionHandler(
            $this->applicationDependencyContainer->getServiceContainer()->getLogger('application'),
        );
    }
}
