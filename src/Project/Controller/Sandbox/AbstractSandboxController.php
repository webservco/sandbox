<?php

declare(strict_types=1);

namespace Project\Controller\Sandbox;

use Override;
use Project\Controller\AbstractController;
use Project\Factory\I18n\LanguageListFactory;
use Project\Factory\I18n\RequestLocalizationContainerFactory;
use Project\View\Sandbox\MainView;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\I18n\Contract\LocalizationContainerInterface;
use WebServCo\I18n\Factory\LanguageLinkViewFactory;
use WebServCo\View\Contract\ViewContainerInterface;

use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * An abstract controller with dependencies specific to current module.
 *
 * The module's pages are in the language of the request (`webservco/i18n`): their views extend
 * `AbstractLocalizedView`, with the container of `getLocalizationContainer()`.
 */
abstract class AbstractSandboxController extends AbstractController
{
    /**
     * The time zone of every date and time shown.
     */
    public const string DISPLAY_TIME_ZONE = 'UTC';

    /**
     * Translator and formatter in the language of the request; created on first use (`getLocalizationContainer()`).
     */
    private ?LocalizationContainerInterface $localizationContainer = null;

    /**
     * Main view (layout): the rendered page, with the language switch.
     */
    #[Override]
    protected function createMainViewContainer(
        ServerRequestInterface $request,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        $localizationContainer = $this->getLocalizationContainer($request);
        $languageListFactory = new LanguageListFactory();
        $languageLinkViewFactory = new LanguageLinkViewFactory($languageListFactory->createLanguageList());

        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new MainView(
                $localizationContainer,
                $this->createCommonView($request),
                $this->viewServicesContainer->getViewRenderer()->renderViewContainer($viewContainer),
                $languageLinkViewFactory->createLanguageLinks($localizationContainer->getTranslator()->getLanguage()),
            ),
            '_main/main.sandbox.default',
        );
    }

    /**
     * Translator and formatter in the language of the request (`LanguageMiddleware`) and the display time zone:
     * for the module's views (`AbstractLocalizedView`).
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
