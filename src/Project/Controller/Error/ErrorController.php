<?php

declare(strict_types=1);

namespace Project\Controller\Error;

use Project\Contract\Controller\ErrorControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use UnexpectedValueException;
use WebServCo\Environment\Contract\EnvironmentInterface;
use WebServCo\Http\Contract\Message\Response\StatusCodeServiceInterface;

use function array_key_exists;
use function is_string;

final class ErrorController extends AbstractErrorController implements ErrorControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $throwable = $request->getAttribute('throwable');

        if (!$throwable instanceof Throwable) {
            throw new UnexpectedValueException('Throwable is not an instance of Throwable.');
        }

        $data = [
            'code' => $this->getDisplayCode($throwable),
            'message' => $this->getDisplayMessage($throwable),
        ];

        // Create view.
        $viewContainer = $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromData($data);

        // Return response.
        return $this->createResponse($viewContainer, $this->getResponseCode($throwable));
    }

    private function getDisplayCode(Throwable $throwable): string
    {
        if ($this->isDevelopment()) {
            return (string) $throwable->getCode();
        }

        return '500';
    }

    /**
     * Get error message to be displayed.
     *
     * Consider:
     * - environment (do not display sensitive information in production),
     * - exception type (some may contain useful information for consumer)
     */
    private function getDisplayMessage(Throwable $throwable): string
    {
        if ($this->isDevelopment()) {
            return $throwable->getMessage();
        }

        return 'Application exception.';
    }

    private function getResponseCode(Throwable $throwable): int
    {
        $code = $throwable->getCode();

        if (is_string($code)) {
            // PDOException for example returns the code as string.
            return 500;
        }

        if ($code < 400) {
            return 500;
        }

        return array_key_exists($code, StatusCodeServiceInterface::STATUS_CODES)
            ? $code
            : 500;
    }

    private function isDevelopment(): bool
    {
        switch (
            $this->getConfigurationGetter()->getString('ENVIRONMENT')
        ) {
            case EnvironmentInterface::ENVIRONMENT_DEVELOPMENT:
            case EnvironmentInterface::ENVIRONMENT_TESTING:
                return true;
            case EnvironmentInterface::ENVIRONMENT_STAGING:
            case EnvironmentInterface::ENVIRONMENT_PRODUCTION:
                return false;
            default:
                throw new UnexpectedValueException('Invalid environment.');
        }
    }
}
