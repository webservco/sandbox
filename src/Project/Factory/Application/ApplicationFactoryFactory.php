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
use WebServCo\Http\Factory\Message\Request\Server\ServerRequestFromServerDataFactory;
use WebServCo\View\Service\ViewRendererResolver;

use function rtrim;

use const DIRECTORY_SEPARATOR;

final class ApplicationFactoryFactory
{
    public function __construct(
        private ApplicationDependencyContainerInterface $applicationDependencyContainer,
        private ExceptionHandlerInterface $exceptionHandler,
    ) {
    }

    public function createServerApplicationFactory(string $projectPath): DefaultServerApplicationFactory
    {
        // Make sure path contains trailing slash (trim + add back).
        $projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return new DefaultServerApplicationFactory(
            $this->createApplicationRunnerFactory($projectPath),
            new DefaultErrorHandlingServiceFactory(),
            $this->applicationDependencyContainer->getServiceContainer(),
            new ServerRequestFromServerDataFactory(),
        );
    }

    private function createApplicationRunnerFactory(string $projectPath): ApplicationRunnerFactory
    {
        // Make sure path contains trailing slash (trim + add back).
        $projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $controllerInstantiatorFactory = $this->createControllerInstantiatorFactory();

        return new ApplicationRunnerFactory(
            new RequestHandlerFactory(
                $this->applicationDependencyContainer,
                $controllerInstantiatorFactory->createControllerInstantiator(
                    $this->applicationDependencyContainer,
                    new SpecificModuleControllerInstantiator(),
                ),
                $this->exceptionHandler,
                $projectPath,
                new ViewRendererResolver(),
            ),
        );
    }

    private function createControllerInstantiatorFactory(): ControllerInstantiatorFactory
    {
        return new ControllerInstantiatorFactory();
    }
}
