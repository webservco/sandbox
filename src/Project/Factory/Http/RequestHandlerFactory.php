<?php

declare(strict_types=1);

namespace Project\Factory\Http;

use Project\Factory\Middleware\ResourceMiddlewareFactory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WebServCo\Http\Contract\Message\Request\RequestHandler\RequestHandlerFactoryInterface;
use WebServCo\Http\Service\Message\Request\Server\ServerHeadersAcceptProcessor;
use WebServCo\Middleware\Service\Log\LoggerMiddleware;
use WebServCo\Middleware\Service\ThreePart\RouteMiddleware;
use WebServCo\Middleware\Service\ViewRenderer\ViewRendererSettingMiddleware;
use WebServCo\Stuff\Contract\RouteInterface;

/**
 * Request handler factory.
 *
 * Creates the general (stack) request handler with all the middleware used in the application.
 */
final class RequestHandlerFactory extends AbstractRequestHandlerFactory implements RequestHandlerFactoryInterface
{
    /**
     * Request handler (PSR-15).
     *
     * "A request handler is an individual component that processes a request
     * and produces a response, as defined by PSR-7."
     *
     * @phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
     */
    public function createRequestHandler(): RequestHandlerInterface
    {
        $stackHandler = $this->createStackHandler();

        // Add application specific middleware next.

        /**
         * Exception handler middleware (1).
         *
         * Will basically only handle application domain exceptions.
         * Two instances:
         * - one as early as possible to handle most exceptions possible.
         * - one as late as possible to benefit from most functionality.
         * Any exceptions thrown before this will be handled by the unhandled exception handler (if implemented).
         * Functionality should be the same as the unhandled exception handler, except for the output;
         * - unhandled exception handler would probably return a very basic output.
         * - exception handler middleware will output a processed response like all the others in the application.
         */
        $exceptionHandlerMiddlewareFactory = $this->createExceptionHandlerMiddlewareFactory();
        $exceptionHandlerMiddleware = $exceptionHandlerMiddlewareFactory->createExceptionHandlerMiddleware();
        $stackHandler->addMiddleware($exceptionHandlerMiddleware);

        // ViewRenderer setting middleware.
        $stackHandler->addMiddleware(new ViewRendererSettingMiddleware(new ServerHeadersAcceptProcessor()));

        // Route middleware; routes requests.
        $stackHandler->addMiddleware(
            new RouteMiddleware(
                '/',
                'sandbox/test',
                // These are `RoutePartsInterface::ROUTE_PART_1` values (only use the part 1 of the processed route).
                ['api', 'sandbox', RouteInterface::ROUTE],
            ),
        );

        $stackHandler->addMiddleware($this->createAuthenticationMiddleware());

        // Request logging middleware.
        $stackHandler->addMiddleware(
            new LoggerMiddleware(
                $this->applicationDependencyContainer->getServiceContainer()->getLogger('application'),
            ),
        );

        // API Authentication middleware
        $stackHandler->addMiddleware($this->createApiAuthenticationMiddleware());

        /**
         * Exception handler middleware (2).
         */
        $stackHandler->addMiddleware($exceptionHandlerMiddleware);

        // Resource middleware; handles requests that are routed by the RouteMiddleware.
        $stackHandler->addMiddleware($this->createResourceMiddleware());

        return $stackHandler;
    }
    /** @phpcs:enable */

    private function createResourceMiddleware(): MiddlewareInterface
    {
        $resourceMiddlewareFactory = new ResourceMiddlewareFactory(
            $this->controllerInstantiator,
            $this->viewRendererResolver,
        );

        return $resourceMiddlewareFactory->createResourceMiddleware($this->projectPath);
    }
}
