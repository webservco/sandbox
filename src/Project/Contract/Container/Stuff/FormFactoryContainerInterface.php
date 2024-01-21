<?php

declare(strict_types=1);

namespace Project\Contract\Container\Stuff;

use Project\Contract\Factory\Stuff\AuthenticationFormFactoryInterface;
use Project\Contract\Factory\Stuff\ItemFormFactoryInterface;
use Project\Contract\Factory\Stuff\SearchItemFormFactoryInterface;

interface FormFactoryContainerInterface
{
    public function getAuthenticationFormFactory(): AuthenticationFormFactoryInterface;

    public function getItemFormFactory(): ItemFormFactoryInterface;

    public function getSearchItemFormFactory(): SearchItemFormFactoryInterface;
}
