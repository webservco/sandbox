<?php

declare(strict_types=1);

namespace Project\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Http\Contract\Message\Response\StatusCodeServiceInterface;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;
use WebServCo\Session\Contract\SessionServiceInterface;
use WebServCo\Stuff\Contract\RouteInterface;

use function ltrim;
use function sprintf;

final class AuthenticationMiddleware implements MiddlewareInterface
{
    public const NEXT_LOCATION_KEY = 'nextLocation';

    public function __construct(
        private DataExtractionContainerInterface $dataExtractionContainer,
        private ResponseFactoryInterface $responseFactory,
        private SessionServiceInterface $sessionService,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Check if condition applies.
        $routePart1 = $request->getAttribute(RoutePartsInterface::ROUTE_PART_1, null);
        if ($routePart1 !== RouteInterface::ROUTE) {
            // Condition does not apply.
            // Pass to the next handler.
            return $handler->handle($request);
        }

        // Start session. Method checks if already started.
        $this->sessionService->start();

        // Check if already authenticated.
        if ($this->isAuthenticated()) {
            // Set Request attribute.
            $request = $request->withAttribute('isAuthenticated', true);

            // Pass to the next handler.
            return $handler->handle($request);
        }

        // Not authenticated.

        $authenticationUrl = sprintf('%s%s/authenticate', $this->composeBaseUrl($request), RouteInterface::ROUTE);
        $currentUrl = $request->getUri()->__toString();

        // Check if current location is the authentication page (prevent endless redirect loop)
        if ($currentUrl === $authenticationUrl) {
            // Pass to the next handler.
            return $handler->handle($request);
        }

        $this->sessionService->setSessionDataItem(self::NEXT_LOCATION_KEY, $this->composeLocation($request));

        // Redirect to authentication.
        return $this->responseFactory->createResponse(StatusCodeServiceInterface::STATUS_SEE_OTHER)->withHeader(
            'Location',
            $authenticationUrl,
        );
    }

    private function composeBaseUrl(ServerRequestInterface $request): string
    {
        return sprintf(
            '%s://%s/',
            $request->getUri()->getScheme(),
            $request->getUri()->getHost(),
        );
    }

    private function composeLocation(ServerRequestInterface $request): string
    {
        $result = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        if ($query !== '') {
            $result .= sprintf('?%s', $query);
        }

        // Remove trailing slash
        return ltrim($result, '/');
    }

    private function isAuthenticated(): bool
    {
        if (!$this->sessionService->isStarted()) {
            // Session not started (eg. CLI) so no authentication.
            return false;
        }

        return $this->dataExtractionContainer->getStrictArrayDataExtractionService()
        ->getBoolean($this->sessionService->getSessionData(), 'isAuthenticated', false);
    }
}
