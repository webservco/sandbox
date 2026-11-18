<?php

declare(strict_types=1);

namespace Project\Factory\I18n;

use Project\Enum\I18n\Language;
use WebServCo\I18n\Contract\LanguageListInterface;
use WebServCo\I18n\Service\LanguageList;

/**
 * The languages (`Language`) as the list the i18n component works with: every place that needs it creates it here.
 */
final class LanguageListFactory
{
    public function createLanguageList(): LanguageListInterface
    {
        return new LanguageList(Language::cases(), Language::DEFAULT);
    }
}
