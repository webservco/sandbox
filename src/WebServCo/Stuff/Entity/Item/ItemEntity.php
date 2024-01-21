<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Entity\Item;

use WebServCo\Data\Contract\Transfer\DataTransferInterface;
use WebServCo\Stuff\DataTransfer\Item\Item;

final class ItemEntity implements DataTransferInterface
{
    public function __construct(public readonly int $id, public readonly Item $item, public readonly ?int $parentItemId)
    {
    }
}
