<?php

declare(strict_types=1);

use Project\View\Stuff\ItemsView;
use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\Stuff\Entity\Item\ItemEntity;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof ItemsView);

$routeUrl = sprintf('%s%s/', $view->commonView->baseUrl, RouteInterface::ROUTE);

$itemAddUrl = sprintf(
    '%sitem%s',
    $routeUrl,
    $view->parentItemEntity !== null ? sprintf('?parent=%d', $view->parentItemEntity->id) : '',
);
?>

<hgroup>
    <nav aria-label="breadcrumb">
        <ul>
            <li>△</li>
            <?php if ($view->parentItemEntityUpstreamGenerator !== null) { ?>
                <?php
                foreach ($view->parentItemEntityUpstreamGenerator as $itemEntity) {
                    if (!$itemEntity instanceof ItemEntity) {
                        throw new OutOfRangeException('Object is not an instance of the required interface.');
                    }
                    ?>
                    <li>
                        <a href="<?=$routeUrl?>items/<?=$itemEntity->id?>"
                           title="<?=$itemEntity->item->name?>">
                            [<?=$itemEntity->id?>]
                            <?=$itemEntity->item->name?>
                        </a>
                    </li>
                    <?php
                }
                ?>
            <?php } ?>
            <?php if ($view->parentItemEntity !== null) { ?>
                <li>
                    <a href="<?=$routeUrl?>items" title="Items">Items</a>
                </li>
            <?php } ?>
        </ul>

    </nav>

    <h2>
        <?php if ($view->parentItemEntity !== null) { ?>
            [<?=$view->parentItemEntity->id?>]
            <?=$view->parentItemEntity->item->name?>
            <small><small><small><small><small>
                <a href="<?=$routeUrl?>item/<?=$view->parentItemEntity->id?>" title="Edit">[edit]</a>
            </small></small></small></small></small>
        <?php } else { ?>
            Items
        <?php } ?>
    </h2>

    <!-- Pico CSS hgroup: ":last-child is muted". Workaround: add a blank element always present. -->
    <span></span>
</hgroup>

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

    <article>
        <a href="<?=$itemAddUrl?>" title="Add item">[Add item]</a>
    </article>
</div>
