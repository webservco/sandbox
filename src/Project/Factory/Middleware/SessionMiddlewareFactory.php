<?php

declare(strict_types=1);

namespace Project\Factory\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Project\Exception\Http\ClientErrorException;
use Project\Factory\Session\SessionStoreFactory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WebServCo\DependencyContainer\Contract\ApplicationDependencyContainerInterface;
use WebServCo\Middleware\Service\Security\CsrfMiddleware;
use WebServCo\Middleware\Service\Session\SessionMiddleware;
use WebServCo\Session\Service\CsrfTokenService;

/**
 * The middleware of the session (`webservco/middleware`): resuming it, and the CSRF check.
 */
final class SessionMiddlewareFactory
{
    // Route part 1 of the API module: it authenticates with a JWT instead of the session cookie.
    private const string API_MODULE = 'api';

    public function __construct(private ApplicationDependencyContainerInterface $applicationDependencyContainer)
    {
    }

    /**
     * A missing or wrong token gets the error page with 403, "page expired": a form left open until its session
     * ended looks the same as a forged one. Every module is checked, except the API module.
     *
     * @param \Psr\Http\Server\RequestHandlerInterface $errorRequestHandler Renders the error page of the `throwable`
     *     request attribute (`ExceptionHandlerMiddlewareFactory::createErrorRequestHandler()`).
     */
    public function createCsrfMiddleware(RequestHandlerInterface $errorRequestHandler): MiddlewareInterface
    {
        $sessionStoreFactory = new SessionStoreFactory($this->applicationDependencyContainer);

        return new CsrfMiddleware(
            new CsrfTokenService($sessionStoreFactory->createSessionStore()),
            $errorRequestHandler,
            new ClientErrorException(
                StatusCodeInterface::STATUS_FORBIDDEN,
                'This page has expired. Go back, reload the page and try again.',
            ),
            [self::API_MODULE],
            $this->applicationDependencyContainer->getServiceContainer()->getLogger('application'),
            $this->applicationDependencyContainer->getRequestServiceContainer()->getServerRequestAttributeService(),
        );
    }

    public function createSessionMiddleware(): MiddlewareInterface
    {
        $sessionStoreFactory = new SessionStoreFactory($this->applicationDependencyContainer);

        return new SessionMiddleware($sessionStoreFactory->createSessionStore());
    }
}
