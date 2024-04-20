<?php

declare(strict_types=1);

namespace Project\View\Stuff;

use Generator;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\View\AbstractView;
use WebServCo\View\View\CommonView;

final class SearchItemView extends AbstractView implements ViewInterface
{
    /**
     * @param \Generator<\WebServCo\Stuff\Entity\Item\ItemEntity> $itemEntityGenerator
     */
    public function __construct(
        public readonly CommonView $commonView,
        public readonly FormInterface $form,
        public readonly Generator $itemEntityGenerator,
    ) {
    }
}
