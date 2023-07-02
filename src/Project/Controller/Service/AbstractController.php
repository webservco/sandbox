<?php

declare(strict_types=1);

namespace Project\Controller\Service;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use WebServCo\Configuration\Contract\ConfigurationGetterInterface;
use WebServCo\Stopwatch\Contract\LapTimerInterface;
use WebServCo\View\Contract\TemplateServiceInterface;
use WebServCo\View\Contract\ViewContainerFactoryInterface;
use WebServCo\View\Contract\ViewContainerInterface;
use WebServCo\View\Contract\ViewRendererInterface;
use WebServCo\View\Service\TemplateService;

use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * An abstract controller with dependencies specific to current project.
 */
abstract class AbstractController
{
    /**
     * Get main ViewContainer to use.
     *
     * Should be customized for each module.
     * Eg. API module could use an API specific structure for the main View with some global data for all responses.
     */
    abstract protected function createMainViewContainer(ViewContainerInterface $viewContainer): ViewContainerInterface;

    public function __construct(
        protected ConfigurationGetterInterface $configurationGetter,
        protected LapTimerInterface $lapTimer,
        protected LoggerInterface $logger,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        protected ViewContainerFactoryInterface $viewContainerFactory,
        protected ViewRendererInterface $viewRenderer,
    ) {
    }

    /**
     * Create Response.
     *
     * Called by individual Controller `handle` method.
     */
    protected function createResponse(ViewContainerInterface $viewContainer, int $code = 200): ResponseInterface
    {
        $this->lapTimer->lap(sprintf('%s: start', __FUNCTION__));

        // Set template service (template group information) to use.
        $templateService = $this->createTemplateService($this->configurationGetter->getString('PROJECT_PATH'));
        $viewContainer->setTemplateService($templateService);

        // Create main View (general page layout containing also the rendered page template).
        $mainViewContainer = $this->createMainViewContainer($viewContainer);
        // Set template service (template group information) to use.
        $mainViewContainer->setTemplateService($templateService);

        // Create Response.
        $response = $this->responseFactory->createResponse($code)
        ->withHeader('Content-Type', $this->viewRenderer->getContentType())
        ->withBody($this->streamFactory->createStream($this->viewRenderer->render($mainViewContainer)));

        $this->lapTimer->lap(sprintf('%s: end', __FUNCTION__));

        return $response;
    }

    /**
     * Create Template service (template group information).
     * Idea: use a configuration of some sort eg. result of middleware processing
     * (different template group based on user preference).
     * We could get an already set attribute from route.
     */
    protected function createTemplateService(string $projectPath): TemplateServiceInterface
    {
        // Make sure path contains trailing slash (trim + add back).
        $projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return new TemplateService(sprintf('%sresources/templates/vanilla', $projectPath), '.php');
    }
}
