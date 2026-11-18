<?php

declare(strict_types=1);

namespace Project\View\Stuff;

use WebServCo\Stuff\Entity\Item\ItemEntity;
use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\View\AbstractView;
use WebServCo\View\View\CommonView;

/**
 * Confirmation of an item's deletion: a POST form to the same URL.
 */
final class ItemDeleteView extends AbstractView implements ViewInterface
{
    public function __construct(
        public readonly CommonView $commonView,
        // The CSRF token of the session, for the form's hidden field.
        public readonly string $csrfToken,
        public readonly ItemEntity $itemEntity,
    ) {
    }
}
