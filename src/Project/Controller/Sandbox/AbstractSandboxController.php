<?php

declare(strict_types=1);

namespace Project\Controller\Sandbox;

use Override;
use Project\Controller\AbstractController;
use Project\Factory\I18n\LanguageListFactory;
use Project\View\Sandbox\MainView;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\I18n\Factory\LanguageLinkViewFactory;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * An abstract controller with dependencies specific to current module.
 *
 * The module's pages are in the language of the request (`webservco/i18n`): their views extend
 * `AbstractLocalizedView`, with the container of `getLocalizationContainer()`.
 */
abstract class AbstractSandboxController extends AbstractController
{
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
}
