<?php

declare(strict_types=1);

namespace Tests\Unit\Resources\I18n;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Project\Enum\I18n\Language;
use Project\Factory\I18n\LanguageListFactory;
use WebServCo\Configuration\Service\PHPConfigurationLoader;
use WebServCo\I18n\Service\CatalogLoader;
use WebServCo\I18n\Service\Check\CatalogReader;
use WebServCo\I18n\Service\Check\DuplicateCaseCheck;
use WebServCo\I18n\Service\Check\LiteralKeyCheck;
use WebServCo\I18n\Service\Check\LookalikeLetterCheck;
use WebServCo\I18n\Service\Check\MessageSyntaxCheck;
use WebServCo\I18n\Service\Check\ParameterCheck;
use WebServCo\I18n\Service\Check\PluralCategoryCheck;
use WebServCo\I18n\Service\Check\SpellingCheck;
use WebServCo\I18n\Service\Check\StructureCheck;

use function dirname;

/**
 * The catalogs (`resources/i18n/`), with the checks of the i18n component (`webservco/i18n`, "Catalog checks"):
 * one test per check, so that a failure names it. Every check reads through one `CatalogReader`.
 *
 * Covers nothing: the checks are in `vendor/`, and the catalogs are not code.
 */
#[CoversNothing]
final class CatalogTest extends TestCase
{
    /**
     * Code directories with literal keys (`LiteralKeyCheck`): relative to the project path.
     */
    private const array SOURCE_DIRECTORIES = ['config', 'resources/templates', 'src'];

    private static ?CatalogReader $catalogReader = null;

    public function testStructure(): void
    {
        self::assertSame([], (new StructureCheck(self::getCatalogReader()))->check());
    }

    public function testMessageSyntax(): void
    {
        self::assertSame([], (new MessageSyntaxCheck(self::getCatalogReader()))->check());
    }

    public function testLookalikeLetters(): void
    {
        self::assertSame([], (new LookalikeLetterCheck(self::getCatalogReader()))->check());
    }

    public function testParameters(): void
    {
        self::assertSame([], (new ParameterCheck(self::getCatalogReader()))->check());
    }

    public function testPluralCategories(): void
    {
        self::assertSame([], (new PluralCategoryCheck(self::getCatalogReader()))->check());
    }

    public function testDuplicateCases(): void
    {
        self::assertSame([], (new DuplicateCaseCheck(self::getCatalogReader()))->check());
    }

    /**
     * English is British English: no common US spellings.
     */
    public function testEnglishSpelling(): void
    {
        self::assertSame([], (new SpellingCheck(self::getCatalogReader(), Language::English))->check());
    }

    public function testLiteralKeys(): void
    {
        $check = new LiteralKeyCheck(self::getCatalogReader(), self::getProjectPath(), self::SOURCE_DIRECTORIES);

        self::assertSame([], $check->check());
    }

    private static function getCatalogReader(): CatalogReader
    {
        if (self::$catalogReader === null) {
            $directoryPath = self::getProjectPath() . 'resources/i18n/';
            $languageListFactory = new LanguageListFactory();
            self::$catalogReader = new CatalogReader(
                new CatalogLoader(new PHPConfigurationLoader(), $directoryPath),
                $languageListFactory->createLanguageList(),
                $directoryPath,
            );
        }

        return self::$catalogReader;
    }

    /**
     * The directory of `composer.json`.
     */
    private static function getProjectPath(): string
    {
        return dirname(__DIR__, 4) . '/';
    }
}
