<?php

declare(strict_types=1);

namespace Project\Factory\Http;

use Override;
use Project\Factory\I18n\LanguageListFactory;
use Project\Factory\Middleware\ResourceMiddlewareFactory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WebServCo\Http\Contract\Message\Request\RequestHandler\RequestHandlerFactoryInterface;
use WebServCo\I18n\Service\LanguageMiddleware;
use WebServCo\I18n\Service\LanguageResolver;
use WebServCo\Middleware\Service\Dynamic\RouteMiddleware;
use WebServCo\Middleware\Service\Log\LoggerMiddleware;
use WebServCo\Middleware\Service\ViewRenderer\ViewRendererSettingMiddleware;
use WebServCo\Stuff\Contract\RouteInterface;

use function str_starts_with;

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
    #[Override]
    public function createRequestHandler(): RequestHandlerInterface
    {
        $stackHandler = $this->createStackHandler();

        // Add application specific middleware next.

        /**
         * Language middleware: the language of the request (`webservco/i18n`).
         *
         * Before the exception handlers, so that error pages can be translated too.
         */
        $stackHandler->addMiddleware($this->createLanguageMiddleware());

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
        // Renders the error page; shared by both exception handlers and the CSRF middleware.
        $errorRequestHandler = $exceptionHandlerMiddlewareFactory->createErrorRequestHandler();
        $exceptionHandlerMiddleware = $exceptionHandlerMiddlewareFactory->createExceptionHandlerMiddleware(
            $errorRequestHandler,
        );
        $stackHandler->addMiddleware($exceptionHandlerMiddleware);

        // ViewRenderer setting middleware.
        $stackHandler->addMiddleware(new ViewRendererSettingMiddleware(
            $this->applicationDependencyContainer->getRequestServiceContainer()->getServerHeadersAcceptService(),
        ));

        // Route middleware; routes requests.
        $stackHandler->addMiddleware(
            new RouteMiddleware(
                '/',
                'sandbox/test',
                // These are part 1 of the route values (only use the part 1 of the processed route).
                ['api', 'sandbox', RouteInterface::ROUTE],
            ),
        );

        $sessionMiddlewareFactory = $this->createSessionMiddlewareFactory();

        /**
         * Session middleware (`webservco/middleware`): resumes the session of a request that sends the session cookie,
         * and sends `Cache-Control: private, no-cache` with a response to a request with a session.
         *
         * Before the authentication middleware, which starts the session for the stuff module.
         * Inside the first exception handler, so that a session that can't start gets the error page;
         * outside the second, so that the error pages it renders get the header too.
         */
        $stackHandler->addMiddleware($sessionMiddlewareFactory->createSessionMiddleware());

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

        /**
         * CSRF middleware (`webservco/middleware`): checks the token of every request that isn't GET, HEAD or OPTIONS,
         * except in the API module.
         *
         * After the route middleware (it reads route part 1) and the session middleware (it needs the resumed session);
         * after the second exception handler, so that the request log has the refused requests.
         */
        $stackHandler->addMiddleware($sessionMiddlewareFactory->createCsrfMiddleware($errorRequestHandler));

        // Resource middleware; handles requests that are routed by the RouteMiddleware.
        $stackHandler->addMiddleware($this->createResourceMiddleware());

        return $stackHandler;
    }
    /** @phpcs:enable */

    private function createLanguageMiddleware(): MiddlewareInterface
    {
        $languageListFactory = new LanguageListFactory();

        return new LanguageMiddleware(
            new LanguageResolver($languageListFactory->createLanguageList()),
            // Secure cookie when the site is served over HTTPS.
            str_starts_with(
                $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
                    ->getString('BASE_URL'),
                'https://',
            ),
        );
    }

    private function createResourceMiddleware(): MiddlewareInterface
    {
        $resourceMiddlewareFactory = new ResourceMiddlewareFactory(
            $this->controllerInstantiator,
            $this->applicationDependencyContainer->getRequestServiceContainer()->getServerRequestAttributeService(),
            $this->viewRendererResolver,
        );

        return $resourceMiddlewareFactory->createResourceMiddleware($this->projectPath);
    }
}
