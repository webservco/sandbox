<?php

declare(strict_types=1);

namespace WebServCo\Stuff\DataTransfer\Item;

use WebServCo\Data\Contract\Transfer\DataTransferInterface;

final class Item implements DataTransferInterface
{
    public function __construct(public readonly string $name, public readonly string $description)
    {
    }
}
