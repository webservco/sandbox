<?php

declare(strict_types=1);

namespace Project\Middleware;

use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;
use WebServCo\Configuration\Contract\ConfigurationGetterInterface;
use WebServCo\Http\Contract\Message\Response\StatusCodeServiceInterface;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;

use function array_key_exists;
use function explode;

final class ApiAuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ConfigurationGetterInterface $configurationGetter,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler,): ResponseInterface
    {
        // Check if condition applies.
        $routePart1 = $request->getAttribute(RoutePartsInterface::ROUTE_PART_1, null);

        if ($routePart1 !== 'api') {
            // Condition does not apply.
            // Pass to the next handler.
            return $handler->handle($request);
        }

        $request->getHeader('Authorization');

        if ($this->verifyAuthorization($request)) {
            // All is OK.
            // Pass to the next handler.
            return $handler->handle($request);
        }

        // Not authorized

        /** @todo Return a more informative response */
        return $this->responseFactory->createResponse(StatusCodeServiceInterface::STATUS_UNAUTHORIZED);
    }

    private function getAuthorizationHeaderValue(ServerRequestInterface $request): string
    {
        $headers = $request->getHeader('Authorization');
        if (!array_key_exists(0, $headers)) {
            throw new UnexpectedValueException('Missing authorization header');
        }

        return $headers[0];
    }

    private function verifyAuthorization(ServerRequestInterface $request): bool
    {
        $authorizationHeaderValue = $this->getAuthorizationHeaderValue($request);

        $parts = explode(' ', $authorizationHeaderValue);

        if (!array_key_exists(1, $parts)) {
            return false;
        }

        if ($parts[0] !== 'Bearer') {
            return false;
        }

        return $parts[1] === $this->configurationGetter->getString('API_KEY');
    }
}
