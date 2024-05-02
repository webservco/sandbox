<?php

declare(strict_types=1);

namespace Project\Factory\Form;

use WebServCo\Form\Service\Filter\StripTagsFilter;
use WebServCo\Form\Service\Filter\TrimFilter;
use WebServCo\Form\Service\Validator\MaximumLengthValidator;
use WebServCo\Form\Service\Validator\MinimumLengthValidator;
use WebServCo\Form\Service\Validator\RequiredValidator;

use function sprintf;

abstract class AbstractFormFactory
{
    private const string ERROR_MESSAGE_MAXIMUM_LENGTH = 'Maximum length: %d chars.';
    private const string ERROR_MESSAGE_MINIMUM_LENGTH = 'Minimum length: %d chars.';
    private const string ERROR_MESSAGE_REQUIRED = 'Field is required.';

    /**
     * @return array<int,\WebServCo\Form\Contract\FormFilterInterface>
     */
    protected function getGeneralFilters(): array
    {
        return [
            new TrimFilter(),
            new StripTagsFilter(),
        ];
    }

    /**
     * @return array<int,\WebServCo\Form\Contract\FormValidatorInterface>
     */
    protected function getGeneralValidators(): array
    {
        return [
            new RequiredValidator(self::ERROR_MESSAGE_REQUIRED),
        ];
    }

    protected function getMaximumLengthValidator(int $maximumLength): MaximumLengthValidator
    {
        return new MaximumLengthValidator(sprintf(self::ERROR_MESSAGE_MAXIMUM_LENGTH, $maximumLength), $maximumLength);
    }

    protected function getMinimumLengthValidator(int $minimumLength): MinimumLengthValidator
    {
        return new MinimumLengthValidator(sprintf(self::ERROR_MESSAGE_MINIMUM_LENGTH, $minimumLength), $minimumLength);
    }
}
