<?php

declare(strict_types=1);

namespace Project\Factory\Middleware;

use Project\Controller\Error\ErrorController;
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

    public function createExceptionHandlerMiddleware(): ExceptionHandlerMiddleware
    {
        return new ExceptionHandlerMiddleware(
            $this->exceptionHandler,
            new ExceptionRequestHandler(
                $this->controllerInstantiator,
                $this->logger,
                $this->viewRendererResolver,
                new RouteConfiguration(ErrorController::class),
            ),
        );
    }
}
