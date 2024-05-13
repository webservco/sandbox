<?php

declare(strict_types=1);

namespace Project\Factory\Form;

use Error;
use Fig\Http\Message\StatusCodeInterface;
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
            new RequiredValidator(new Error(self::ERROR_MESSAGE_REQUIRED, StatusCodeInterface::STATUS_BAD_REQUEST)),
        ];
    }

    protected function getMaximumLengthValidator(int $maximumLength): MaximumLengthValidator
    {
        return new MaximumLengthValidator(
            new Error(
                sprintf(self::ERROR_MESSAGE_MAXIMUM_LENGTH, $maximumLength),
                StatusCodeInterface::STATUS_BAD_REQUEST,
            ),
            $maximumLength,
        );
    }

    protected function getMinimumLengthValidator(int $minimumLength): MinimumLengthValidator
    {
        return new MinimumLengthValidator(
            new Error(
                sprintf(self::ERROR_MESSAGE_MINIMUM_LENGTH, $minimumLength),
                StatusCodeInterface::STATUS_BAD_REQUEST,
            ),
            $minimumLength,
        );
    }
}
