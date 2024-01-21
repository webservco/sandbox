<?php

declare(strict_types=1);

namespace Project\Container\Stuff;

use Project\Contract\Container\Stuff\FormFactoryContainerInterface;
use Project\Contract\Factory\Stuff\AuthenticationFormFactoryInterface;
use Project\Contract\Factory\Stuff\ItemFormFactoryInterface;
use Project\Contract\Factory\Stuff\SearchItemFormFactoryInterface;
use Project\Factory\Form\AuthenticationFormFactory;
use Project\Factory\Form\ItemFormFactory;
use Project\Factory\Form\SearchItemFormFactory;
use WebServCo\Configuration\Contract\ConfigurationGetterInterface;

final class FormFactoryContainer implements FormFactoryContainerInterface
{
    private ?AuthenticationFormFactoryInterface $authenticationFormFactory = null;
    private ?ItemFormFactoryInterface $itemFormFactory = null;
    private ?SearchItemFormFactoryInterface $searchItemFormFactory = null;

    public function __construct(private ConfigurationGetterInterface $configurationGetter)
    {
    }

    public function getAuthenticationFormFactory(): AuthenticationFormFactoryInterface
    {
        if ($this->authenticationFormFactory === null) {
            $this->authenticationFormFactory = new AuthenticationFormFactory($this->configurationGetter);
        }

        return $this->authenticationFormFactory;
    }

    public function getItemFormFactory(): ItemFormFactoryInterface
    {
        if ($this->itemFormFactory === null) {
            $this->itemFormFactory = new ItemFormFactory();
        }

        return $this->itemFormFactory;
    }

    public function getSearchItemFormFactory(): SearchItemFormFactoryInterface
    {
        if ($this->searchItemFormFactory === null) {
            $this->searchItemFormFactory = new SearchItemFormFactory();
        }

        return $this->searchItemFormFactory;
    }
}
