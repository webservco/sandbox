<?php

declare(strict_types=1);

namespace Project\View\Sandbox;

use DateTimeImmutable;
use WebServCo\I18n\Contract\LocalizationContainerInterface;
use WebServCo\I18n\View\AbstractLocalizedView;
use WebServCo\View\Contract\ViewInterface;

final class TestView extends AbstractLocalizedView implements ViewInterface
{
    /**
     * @param list<int> $counts Plural examples.
     */
    public function __construct(
        LocalizationContainerInterface $localizationContainer,
        public readonly int $intProperty,
        public readonly ?string $stringProperty,
        // Date example.
        public readonly DateTimeImmutable $dateTime,
        // Number example.
        public readonly float $number,
        public readonly array $counts,
    ) {
        parent::__construct($localizationContainer);
    }
}
