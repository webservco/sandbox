<?php

declare(strict_types=1);

namespace Project\Factory\Middleware;

use Project\RequestHandler\ThreePart\ApiRequestHandler;
use Project\RequestHandler\ThreePart\SandboxRequestHandler;
use Project\RequestHandler\ThreePart\StuffRequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WebServCo\Configuration\Service\PHPConfigurationLoader;
use WebServCo\Controller\Contract\ControllerInstantiatorInterface;
use WebServCo\Middleware\Service\ThreePart\ResourceMiddleware;
use WebServCo\Route\Service\ControllerView\RoutesConfigurationLoader;
use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\View\Contract\ViewRendererResolverInterface;

use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

final class ResourceMiddlewareFactory
{
    public function __construct(
        private ControllerInstantiatorInterface $controllerInstantiator,
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

    private function createApiRequestHandler(
        ControllerInstantiatorInterface $controllerInstantiator,
        string $projectPath,
        ViewRendererResolverInterface $viewRendererResolver,
    ): RequestHandlerInterface {
        return new ApiRequestHandler(
            $controllerInstantiator,
            $viewRendererResolver,
            $this->getRoutesConfiguration($projectPath, 'API'),
        );
    }

    private function createSandboxRequestHandler(
        ControllerInstantiatorInterface $controllerInstantiator,
        string $projectPath,
        ViewRendererResolverInterface $viewRendererResolver,
    ): RequestHandlerInterface {
        return new SandboxRequestHandler(
            $controllerInstantiator,
            $viewRendererResolver,
            $this->getRoutesConfiguration($projectPath, 'Sandbox'),
        );
    }

    private function createStuffRequestHandler(
        ControllerInstantiatorInterface $controllerInstantiator,
        string $projectPath,
        ViewRendererResolverInterface $viewRendererResolver,
    ): RequestHandlerInterface {
        return new StuffRequestHandler(
            $controllerInstantiator,
            $viewRendererResolver,
            $this->getRoutesConfiguration($projectPath, 'Stuff'),
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
            'api' => $this->createApiRequestHandler($controllerInstantiator, $projectPath, $viewRendererResolver),
            // Request handler for /sandbox requests.
            'sandbox' => $this->createSandboxRequestHandler(
                $controllerInstantiator,
                $projectPath,
                $viewRendererResolver,
            ),
            // Request handler for /stuff requests.
            RouteInterface::ROUTE => $this->createStuffRequestHandler(
                $controllerInstantiator,
                $projectPath,
                $viewRendererResolver,
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
