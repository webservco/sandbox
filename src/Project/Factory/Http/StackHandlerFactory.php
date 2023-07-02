<?php

declare(strict_types=1);

namespace Project\Factory\Http;

use Project\Controller\Service\Error\NotFoundController;
use Project\Factory\View\Container\Error\NotFoundViewContainerFactory;
use Project\RequestHandler\Exception\ExceptionRequestHandler;
use Psr\Log\LoggerInterface;
use WebServCo\Controller\Contract\ControllerInstantiatorInterface;
use WebServCo\Http\Contract\Message\Request\RequestHandler\RequestHandlerFactoryInterface;
use WebServCo\Http\Service\Message\Request\RequestHandler\StackHandler;
use WebServCo\Route\Service\ControllerView\RouteConfiguration;
use WebServCo\View\Contract\ViewRendererResolverInterface;

final class StackHandlerFactory implements RequestHandlerFactoryInterface
{
    public function __construct(
        private ControllerInstantiatorInterface $controllerInstantiator,
        private LoggerInterface $logger,
        private ViewRendererResolverInterface $viewRendererResolver,
    ) {
    }

    public function createRequestHandler(): StackHandler
    {
        return new StackHandler(
            // fallbackHandler
            new ExceptionRequestHandler(
                $this->controllerInstantiator,
                $this->logger,
                $this->viewRendererResolver,
                new RouteConfiguration(NotFoundController::class, NotFoundViewContainerFactory::class),
            ),
        );
    }
}
