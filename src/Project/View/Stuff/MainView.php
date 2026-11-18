<?php

declare(strict_types=1);

namespace Project\View\Stuff;

use WebServCo\I18n\Contract\LocalizationContainerInterface;
use WebServCo\I18n\View\AbstractLocalizedView;
use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\View\CommonView;

/**
 * Main view (layout) of the stuff module, `_main/main.stuff.pico` and `_main/main.stuff.notauthenticated.pico`:
 * the rendered page, the flash messages and the CSRF token of the search form.
 *
 * Takes the place of `WebServCo\View\View\MainView`. Localized for the flash messages only: they are in the language
 * of the request, the module's pages are in English.
 */
final class MainView extends AbstractLocalizedView implements ViewInterface
{
    /**
     * @param list<\WebServCo\Session\Model\DataTransfer\FlashMessage> $flashMessages Taken from the session after the
     *     page was rendered: shown once.
     */
    public function __construct(
        LocalizationContainerInterface $localizationContainer,
        public readonly CommonView $commonView,
        // The CSRF token of the session, for the search form.
        public readonly string $csrfToken,
        // The rendered page template.
        public readonly string $data,
        public readonly array $flashMessages,
    ) {
        parent::__construct($localizationContainer);
    }
}
