<?php

declare(strict_types=1);

namespace Project\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Http\Contract\Message\Request\Server\ServerRequestAttributeServiceInterface;
use WebServCo\Http\Contract\Message\Response\StatusCodeServiceInterface;
use WebServCo\Session\Contract\SessionServiceInterface;
use WebServCo\Stuff\Contract\RouteInterface;

use function ltrim;
use function sprintf;

final class AuthenticationMiddleware implements MiddlewareInterface
{
    public const string NEXT_LOCATION_KEY = 'nextLocation';
    public const string REQUEST_ATTRIBUTE_KEY_USER_ID = 'userId';

    public function __construct(
        private DataExtractionContainerInterface $dataExtractionContainer,
        private ResponseFactoryInterface $responseFactory,
        private ServerRequestAttributeServiceInterface $serverRequestAttributeService,
        private SessionServiceInterface $sessionService,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Check if condition applies.
        $routePart1 = $this->serverRequestAttributeService->getRoutePart(1, $request);
        if ($routePart1 !== RouteInterface::ROUTE) {
            // Condition does not apply.
            // Pass to the next handler.
            return $handler->handle($request);
        }

        // Start session. Method checks if already started.
        $this->sessionService->start();

        // Check if already authenticated.
        if ($this->isAuthenticated()) {
            return $this->handleAuthenticated($request, $handler);
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

    private function getUserIdFromSession(): ?string
    {
        if (!$this->sessionService->isStarted()) {
            // Session not started (eg. CLI) so no authentication.
            return null;
        }

        return $this->dataExtractionContainer->getStrictArrayDataExtractionService()
        ->getNullableString($this->sessionService->getSessionData(), self::REQUEST_ATTRIBUTE_KEY_USER_ID, null);
    }

    private function handleAuthenticated(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $userId = $this->getUserIdFromSession();
        if ($userId === null) {
            throw new UnexpectedValueException('userId is null.');
        }

        // Set Request attributes.
        $request = $request->withAttribute('isAuthenticated', true);
        $request = $request->withAttribute(self::REQUEST_ATTRIBUTE_KEY_USER_ID, $userId);

        // Pass to the next handler.
        return $handler->handle($request);
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
