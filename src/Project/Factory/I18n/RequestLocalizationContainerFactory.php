<?php

declare(strict_types=1);

namespace Project\Factory\I18n;

use DateTimeZone;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use WebServCo\Configuration\Service\PHPConfigurationLoader;
use WebServCo\I18n\Contract\LocalizationContainerInterface;
use WebServCo\I18n\Factory\LocalizationContainerFactory;
use WebServCo\I18n\Service\CatalogLoader;
use WebServCo\I18n\Service\RequestLanguageReader;

/**
 * Creates the localization services of a request: the translator and the formatter of the i18n component, with the
 * project's catalogs and languages, in the language `LanguageMiddleware` gave the request.
 */
final class RequestLocalizationContainerFactory
{
    public function __construct(private string $catalogDirectoryPath, private LoggerInterface $logger)
    {
    }

    /**
     * @param non-empty-string $timeZone The display time zone: an identifier (`UTC`, `Europe/Berlin`).
     */
    public function createLocalizationContainer(
        ServerRequestInterface $request,
        string $timeZone,
    ): LocalizationContainerInterface {
        $languageListFactory = new LanguageListFactory();
        $localizationContainerFactory = new LocalizationContainerFactory(
            new CatalogLoader(new PHPConfigurationLoader(), $this->catalogDirectoryPath),
            $languageListFactory->createLanguageList()->getDefaultLanguage(),
            // A key missing in a language is a warning.
            $this->logger,
        );
        $requestLanguageReader = new RequestLanguageReader();

        return $localizationContainerFactory->createLocalizationContainer(
            $requestLanguageReader->getLanguage($request),
            new DateTimeZone($timeZone),
        );
    }
}
