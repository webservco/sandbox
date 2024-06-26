<?php

declare(strict_types=1);

namespace Project\Controller\Error;

use Fig\Http\Message\StatusCodeInterface;
use Project\Contract\Controller\ErrorControllerInterface;
use Project\View\Error\ErrorView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use UnexpectedValueException;
use WebServCo\Environment\Contract\EnvironmentInterface;
use WebServCo\Http\Contract\Message\Response\StatusCodeServiceInterface;
use WebServCo\View\Contract\ViewContainerInterface;

use function array_key_exists;
use function is_string;

final class ErrorController extends AbstractErrorController implements ErrorControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data processing would go here (use services).
        $throwable = $request->getAttribute('throwable');

        if (!$throwable instanceof Throwable) {
            throw new UnexpectedValueException('Throwable is not an instance of Throwable.');
        }

        // Create view.
        $viewContainer = $this->createViewContainer($throwable);

        // Return response.
        return $this->createResponse($request, $viewContainer, $this->getResponseCode($throwable));
    }

    private function createViewContainer(Throwable $throwable): ViewContainerInterface
    {
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new ErrorView(
                $this->getDisplayCode($throwable),
                $this->getDisplayMessage($throwable),
            ),
            'error/error',
        );
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
            return StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
        }

        if ($code < 400) {
            return StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
        }

        return array_key_exists($code, StatusCodeServiceInterface::STATUS_CODES)
            ? $code
            : StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
    }

    private function isDevelopment(): bool
    {
        switch (
            $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
            ->getString('ENVIRONMENT')
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
