<?php

declare(strict_types=1);

namespace Project\View\Stuff;

use Generator;
use WebServCo\Stuff\Entity\Item\ItemEntity;
use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\View\AbstractView;
use WebServCo\View\View\CommonView;

final class ItemsView extends AbstractView implements ViewInterface
{
    /**
     * @param \Generator<\WebServCo\Stuff\Entity\Item\ItemEntity> $itemEntityGenerator
     * @param ?\Generator<\WebServCo\Stuff\Entity\Item\ItemEntity> $parentItemEntityUpstreamGenerator
     */
    public function __construct(
        public readonly CommonView $commonView,
        public readonly Generator $itemEntityGenerator,
        public readonly ?ItemEntity $parentItemEntity,
        public readonly ?Generator $parentItemEntityUpstreamGenerator,
    ) {
    }
}
