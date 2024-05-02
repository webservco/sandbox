<?php

declare(strict_types=1);

namespace Project\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use WebServCo\Controller\Service\AbstractDefaultController;
use WebServCo\View\Contract\TemplateServiceInterface;
use WebServCo\View\Service\TemplateService;

use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * An abstract controller with dependencies specific to current project.
 */
abstract class AbstractController extends AbstractDefaultController
{
    protected function createLocalRedirectResponse(string $location): ResponseInterface
    {
        return $this->createRedirectResponse(
            sprintf(
                '%s%s',
                $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
                ->getString('BASE_URL'),
                $location,
            ),
            StatusCodeInterface::STATUS_SEE_OTHER,
        );
    }

    /**
     * Create Template service (template group information).
     *
     * Should be customized at application level.
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
