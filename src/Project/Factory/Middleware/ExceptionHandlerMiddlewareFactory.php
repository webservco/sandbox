<?php

declare(strict_types=1);

namespace Project\Factory\Middleware;

use Project\Controller\Error\ErrorController;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use WebServCo\Controller\Contract\ControllerInstantiatorInterface;
use WebServCo\Exception\Contract\ExceptionHandlerInterface;
use WebServCo\Http\Service\Message\Request\RequestHandler\Exception\ExceptionRequestHandler;
use WebServCo\Middleware\Service\Exception\ExceptionHandlerMiddleware;
use WebServCo\Route\Service\ControllerView\RouteConfiguration;
use WebServCo\View\Contract\ViewRendererResolverInterface;

final class ExceptionHandlerMiddlewareFactory
{
    public function __construct(
        private ControllerInstantiatorInterface $controllerInstantiator,
        private ExceptionHandlerInterface $exceptionHandler,
        private LoggerInterface $logger,
        private ViewRendererResolverInterface $viewRendererResolver,
    ) {
    }

    /**
     * Renders the error page (`ErrorController`) of the `throwable` request attribute.
     *
     * Used by the exception handler middleware, and by the CSRF middleware for a refused request.
     */
    public function createErrorRequestHandler(): RequestHandlerInterface
    {
        return new ExceptionRequestHandler(
            $this->controllerInstantiator,
            $this->logger,
            $this->viewRendererResolver,
            new RouteConfiguration(ErrorController::class),
        );
    }

    public function createExceptionHandlerMiddleware(
        RequestHandlerInterface $errorRequestHandler,
    ): ExceptionHandlerMiddleware {
        return new ExceptionHandlerMiddleware($this->exceptionHandler, $errorRequestHandler);
    }
}
