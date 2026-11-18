<?php

declare(strict_types=1);

namespace Project\Enum\I18n;

use Override;
use WebServCo\I18n\Contract\LanguageInterface;

/**
 * The languages of the sandbox module (`webservco/i18n`). The value is the language code (`getCode()`): the catalog
 * directory (`resources/i18n/{code}/`), the `?lang=` value, the `lang` attribute and `Content-Language`.
 * The ICU locale (formats, plural rules) is `getLocale()`.
 *
 * Cases are in the order of the language switch: alphabetical by native name. `LanguageListFactory` makes the list.
 */
enum Language: string implements LanguageInterface
{
    case German = 'de';
    case English = 'en';

    /**
     * The reference language: its catalogs have every key, and a key missing in another language falls back to it.
     */
    public const Language DEFAULT = self::English;

    #[Override]
    public function getCode(): string
    {
        return $this->value;
    }

    /**
     * ICU locale of messages, dates, times and numbers.
     * English is British English (`5 Nov 2026`, `21:07`), not ICU's `en`, which is US (`Nov 5, 2026`, `9:07 PM`).
     */
    #[Override]
    public function getLocale(): string
    {
        return match ($this) {
            self::German => 'de',
            self::English => 'en_GB',
        };
    }

    #[Override]
    public function getNativeName(): string
    {
        return match ($this) {
            self::German => 'Deutsch',
            self::English => 'English',
        };
    }
}
