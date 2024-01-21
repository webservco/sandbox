<?php

declare(strict_types=1);

namespace Project\Contract\Factory\Stuff;

use WebServCo\Form\Contract\FormFactoryInterface;
use WebServCo\Stuff\DataTransfer\Item\Item;

interface ItemFormFactoryInterface extends FormFactoryInterface
{
    public function setItem(Item $item): bool;
}
