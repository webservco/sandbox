<?php

declare(strict_types=1);

namespace Project\Middleware\API;

use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;
use WebServCo\Configuration\Contract\ConfigurationGetterInterface;
use WebServCo\Http\Contract\Message\Request\Server\ServerRequestAttributeServiceInterface;
use WebServCo\Http\Contract\Message\Response\StatusCodeServiceInterface;
use WebServCo\JWT\Contract\DecoderServiceInterface;
use WebServCo\JWT\DataTransfer\Payload;

use function array_key_exists;
use function explode;

final class ApiAuthenticationMiddleware implements MiddlewareInterface
{
    public const string REQUEST_ATTRIBUTE_KEY_USER_ID = 'userId';

    private ?Payload $jwtPayload = null;

    public function __construct(
        private ConfigurationGetterInterface $configurationGetter,
        private DecoderServiceInterface $decoderService,
        private ResponseFactoryInterface $responseFactory,
        private ServerRequestAttributeServiceInterface $serverRequestAttributeService,
    ) {
    }

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Check if condition applies.
        $routePart1 = $this->serverRequestAttributeService->getRoutePart(1, $request);

        if ($routePart1 !== 'api') {
            // Condition does not apply.
            // Pass to the next handler.
            return $handler->handle($request);
        }

        $request->getHeader('Authorization');

        if ($this->verifyAuthorization($request)) {
            // All is OK.

            if ($this->jwtPayload === null) {
                // Sanity check, should never arrive here.
                throw new UnexpectedValueException('JWT payload is null.');
            }

            // Set Request attribute.
            $request = $request->withAttribute(self::REQUEST_ATTRIBUTE_KEY_USER_ID, $this->jwtPayload->sub);

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

        $this->jwtPayload = $this->decoderService->decodeJwt($parts[1]);

        return $this->jwtPayload->iss === $this->configurationGetter->getString('API_KEY');
    }
}
