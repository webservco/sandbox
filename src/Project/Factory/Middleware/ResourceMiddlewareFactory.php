<?php

declare(strict_types=1);

namespace Project\Factory\Middleware;

use Project\RequestHandler\ThreePart\ApiRequestHandler;
use Project\RequestHandler\ThreePart\SandboxRequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use WebServCo\Configuration\Service\PHPConfigurationLoader;
use WebServCo\Controller\Contract\ControllerInstantiatorInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;
use WebServCo\Middleware\Service\ThreePart\ResourceMiddleware;
use WebServCo\Route\Service\ControllerView\RoutesConfigurationLoader;
use WebServCo\View\Contract\ViewRendererResolverInterface;

use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

final class ResourceMiddlewareFactory
{
    public function __construct(
        private ControllerInstantiatorInterface $controllerInstantiator,
        private LocalDependencyContainerInterface $localDependencyContainer,
        private ViewRendererResolverInterface $viewRendererResolver,
    ) {
    }

    public function createResourceMiddleware(string $projectPath): MiddlewareInterface
    {
        // Make sure path contains trailing slash (trim + add back).
        $projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return new ResourceMiddleware(
            // List of requests handlers for this middleware.
            $this->getResouceMiddlewareHandlers(
                $this->controllerInstantiator,
                $projectPath,
                $this->viewRendererResolver,
            ),
        );
    }

    /**
     * Get list of request handlers.
     *
     * This will be a list of already instantiated classes, make sure they are "lightweight" and few in numbers.
     * They will be general application section handlers (eg. APIHandler, BackofficeHandler, FrontendHandler)
     * and not specific (eg. Controllers); avoid loading too many classes.
     *
     * Refactoring idea: use a static list here and then in implementing class
     * validate that there is a handler available using this list, and instantiate only that specific handler.
     *
     * @return array<string,\Psr\Http\Server\RequestHandlerInterface>
     */
    private function getResouceMiddlewareHandlers(
        ControllerInstantiatorInterface $controllerInstantiator,
        string $projectPath,
        ViewRendererResolverInterface $viewRendererResolver,
    ): array {
        // Make sure path contains trailing slash (trim + add back).
        $projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return [
            // Request handler for /api requests.
            'api' => new ApiRequestHandler(
                $controllerInstantiator,
                $this->localDependencyContainer,
                $viewRendererResolver,
                $this->getRoutesConfiguration($projectPath, 'API'),
            ),
            // Request handler for /sandbox requests.
            'sandbox' => new SandboxRequestHandler(
                $controllerInstantiator,
                $this->localDependencyContainer,
                $viewRendererResolver,
                $this->getRoutesConfiguration($projectPath, 'Sandbox'),
            ),
        ];
    }

    /**
     * @return array<string,\WebServCo\Route\Service\ControllerView\RouteConfiguration>
     */
    private function getRoutesConfiguration(string $projectPath, string $configurationType): array
    {
        // Make sure path contains trailing slash (trim + add back).
        $projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $routesConfigFile = sprintf(
            '%sconfig%s%s%sRoutes.php',
            $projectPath,
            DIRECTORY_SEPARATOR,
            $configurationType,
            DIRECTORY_SEPARATOR,
        );

        $routesConfigLoader = new RoutesConfigurationLoader(new PHPConfigurationLoader());

        return $routesConfigLoader->loadFromFile($routesConfigFile);
    }
}
