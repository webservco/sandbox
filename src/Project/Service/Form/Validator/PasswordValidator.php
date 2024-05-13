<?php

declare(strict_types=1);

namespace Project\Service\Form\Validator;

use Error;
use Fig\Http\Message\StatusCodeInterface;
use Throwable;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormValidatorInterface;

final class PasswordValidator implements FormValidatorInterface
{
    public function __construct(private string $authenticationPassword)
    {
    }

    public function getError(): Throwable
    {
        return new Error('Invalid password.', StatusCodeInterface::STATUS_BAD_REQUEST);
    }

    public function validate(FormFieldInterface $formField): bool
    {
        return $formField->getValue() === $this->authenticationPassword;
    }
}
