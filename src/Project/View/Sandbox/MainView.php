<?php

declare(strict_types=1);

namespace Project\View\Sandbox;

use WebServCo\I18n\Contract\LocalizationContainerInterface;
use WebServCo\I18n\View\AbstractLocalizedView;
use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\View\CommonView;

/**
 * Main view (layout) of the sandbox module, `_main/main.sandbox.default`: the rendered page, in the language of the
 * request, with the language switch.
 *
 * Takes the place of `WebServCo\View\View\MainView`, which has no localization.
 */
final class MainView extends AbstractLocalizedView implements ViewInterface
{
    /**
     * @param list<\WebServCo\I18n\View\LanguageLinkView> $languageLinks The language switch.
     */
    public function __construct(
        LocalizationContainerInterface $localizationContainer,
        public readonly CommonView $commonView,
        // The rendered page template.
        public readonly string $data,
        public readonly array $languageLinks,
    ) {
        parent::__construct($localizationContainer);
    }
}
