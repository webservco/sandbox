<?php

declare(strict_types=1);

namespace Project\Factory\Form;

use Project\Contract\Factory\Stuff\AuthenticationFormFactoryInterface;
use WebServCo\Configuration\Contract\ConfigurationGetterInterface;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Form\Service\Form;
use WebServCo\Form\Service\FormField;
use WebServCo\Stuff\Service\Form\Validator\PasswordValidator;

final class AuthenticationFormFactory extends AbstractFormFactory implements AuthenticationFormFactoryInterface
{
    public function __construct(private ConfigurationGetterInterface $configurationGetter)
    {
    }

    public function createForm(): FormInterface
    {
        return new Form(
            [
                0 => $this->createPasswordField(),
            ],
            $this->getGeneralFilters(),
            $this->getGeneralValidators(),
        );
    }

    private function createPasswordField(): FormFieldInterface
    {
        return new FormField(
            [
            ],
            'password',
            true,
            'password',
            'Password',
            'Password',
            [
                $this->getMinimumLengthValidator(6),
                $this->getMaximumLengthValidator(90),
                new PasswordValidator($this->configurationGetter->getString('AUTHENTICATION_PASSWORD')),
            ],
            null,
        );
    }
}
