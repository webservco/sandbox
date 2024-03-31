<?php

declare(strict_types=1);

namespace Project\Service\Form\Validator;

use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormValidatorInterface;

final class PasswordValidator implements FormValidatorInterface
{
    public function __construct(private string $authenticationPassword)
    {
    }

    public function getErrorMessage(): string
    {
        return 'Invalid password.';
    }

    public function validate(FormFieldInterface $formField): bool
    {
        return $formField->getValue() === $this->authenticationPassword;
    }
}
