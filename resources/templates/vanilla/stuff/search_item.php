<?php

declare(strict_types=1);

use Project\View\Stuff\SearchItemView;
use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\Stuff\Entity\Item\ItemEntity;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof SearchItemView);

$routeUrl = sprintf('%s%s/', $view->commonView->baseUrl, RouteInterface::ROUTE);
?>
<h2>
    Search
    <small>
        <kbd>
            <?=$view->escape($view->form->getField('search_item')->getValue())?>
        </kbd>
    </small>
</h2>

<hr>

<div>
    <?php
    foreach ($view->itemEntityGenerator as $itemEntity) {
        if (!$itemEntity instanceof ItemEntity) {
            throw new OutOfRangeException('Object is not an instance of the required interface.');
        }
        ?>
        <article>
            [<?=$itemEntity->id?>]
            <a href="<?=$routeUrl?>items/<?=$itemEntity->id?>"
               title="<?=$itemEntity->item->name?>"><?=$itemEntity->item->name?></a>
            <small>
                <a href="<?=$routeUrl?>item/<?=$itemEntity->id?>"
                   title="Edit item">[edit]</a>
            </small>
            <br>
            <small><i><?=$itemEntity->item->description?></i></small>
        </article>
    <?php } ?>
</div>
