<?php

declare(strict_types=1);

namespace Project\View\Stuff;

use WebServCo\Form\Contract\FormInterface;
use WebServCo\Stuff\Entity\Item\ItemEntity;
use WebServCo\View\AbstractView;
use WebServCo\View\CommonView;
use WebServCo\View\Contract\ViewInterface;

/**
 * Individual item.
 */
final class ItemView extends AbstractView implements ViewInterface
{
    public function __construct(
        public readonly CommonView $commonView,
        public readonly FormInterface $form,
        public readonly ?ItemEntity $itemEntity,
    ) {
    }
}
