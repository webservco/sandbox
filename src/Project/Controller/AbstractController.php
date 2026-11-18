<?php

declare(strict_types=1);

namespace Project\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Override;
use Project\Factory\I18n\RequestLocalizationContainerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Controller\Service\AbstractDefaultController;
use WebServCo\I18n\Contract\LocalizationContainerInterface;
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
    /**
     * The time zone of every date and time shown.
     */
    public const string DISPLAY_TIME_ZONE = 'UTC';

    /**
     * Translator and formatter in the language of the request; created on first use (`getLocalizationContainer()`).
     */
    private ?LocalizationContainerInterface $localizationContainer = null;

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
    #[Override]
    protected function createTemplateService(string $projectPath): TemplateServiceInterface
    {
        // Make sure path contains trailing slash (trim + add back).
        $projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return new TemplateService(sprintf('%sresources/templates/vanilla', $projectPath), '.php');
    }

    /**
     * Translator and formatter in the language of the request (`LanguageMiddleware`) and the display time zone:
     * for localized views (`AbstractLocalizedView`).
     *
     * The Sandbox module's pages, and the Stuff module's flash messages.
     */
    protected function getLocalizationContainer(ServerRequestInterface $request): LocalizationContainerInterface
    {
        return $this->localizationContainer ??= $this->createLocalizationContainerFactory()
            ->createLocalizationContainer($request, self::DISPLAY_TIME_ZONE);
    }

    private function createLocalizationContainerFactory(): RequestLocalizationContainerFactory
    {
        $serviceContainer = $this->applicationDependencyContainer->getServiceContainer();

        return new RequestLocalizationContainerFactory(
            sprintf(
                '%s%sresources/i18n',
                // Make sure path contains trailing slash (trim + add back).
                rtrim($serviceContainer->getConfigurationGetter()->getString('PROJECT_PATH'), DIRECTORY_SEPARATOR),
                DIRECTORY_SEPARATOR,
            ),
            $serviceContainer->getLogger('application'),
        );
    }
}
