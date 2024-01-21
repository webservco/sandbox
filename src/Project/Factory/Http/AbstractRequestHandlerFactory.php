<?php

declare(strict_types=1);

namespace Project\Factory\Http;

use Project\Factory\Middleware\ExceptionHandlerMiddlewareFactory;
use Project\Middleware\AuthenticationMiddleware;
use WebServCo\Controller\Contract\ControllerInstantiatorInterface;
use WebServCo\DependencyContainer\Contract\ApplicationDependencyContainerInterface;
use WebServCo\Exception\Contract\ExceptionHandlerInterface;
use WebServCo\Http\Contract\Message\Request\RequestHandler\RequestHandlerFactoryInterface;
use WebServCo\Http\Service\Message\Request\RequestHandler\StackHandler;
use WebServCo\View\Contract\ViewRendererResolverInterface;

use function rtrim;

use const DIRECTORY_SEPARATOR;

/**
 * Request handler factory.
 *
 * Creates the general (stack) request handler with all the middleware used in the application.
 */
abstract class AbstractRequestHandlerFactory implements RequestHandlerFactoryInterface
{
    public function __construct(
        protected ApplicationDependencyContainerInterface $applicationDependencyContainer,
        protected ControllerInstantiatorInterface $controllerInstantiator,
        private ExceptionHandlerInterface $exceptionHandler,
        protected string $projectPath,
        protected ViewRendererResolverInterface $viewRendererResolver,
    ) {
        // Make sure path contains trailing slash (trim + add back).
        $this->projectPath = rtrim($this->projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    protected function createAuthenticationMiddleware(): AuthenticationMiddleware
    {
        return new AuthenticationMiddleware(
            $this->applicationDependencyContainer->getDataExtractionContainer(),
            $this->applicationDependencyContainer->getFactoryContainer()->getResponseFactory(),
            $this->applicationDependencyContainer->getServiceContainer()->getSessionService(),
        );
    }

    protected function createExceptionHandlerMiddlewareFactory(): ExceptionHandlerMiddlewareFactory
    {
        return new ExceptionHandlerMiddlewareFactory(
            $this->controllerInstantiator,
            $this->exceptionHandler,
            $this->applicationDependencyContainer->getServiceContainer()->getLogger('application'),
            $this->viewRendererResolver,
        );
    }

    protected function createStackHandler(): StackHandler
    {
        /**
         * The general request handler that processes all the middleware.
         */
        $stackHandlerFactory = new StackHandlerFactory(
            $this->controllerInstantiator,
            $this->applicationDependencyContainer->getServiceContainer()->getLogger('application'),
            $this->viewRendererResolver,
        );

        return $stackHandlerFactory->createRequestHandler();
    }
}
