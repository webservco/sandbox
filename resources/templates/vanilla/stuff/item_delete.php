<?php

declare(strict_types=1);

use Project\View\Stuff\ItemDeleteView;
use WebServCo\Session\Contract\CsrfTokenServiceInterface;
use WebServCo\Stuff\Contract\RouteInterface;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof ItemDeleteView);

$routeUrl = sprintf('%s%s/', $view->commonView->baseUrl, RouteInterface::ROUTE);
?>
<h2>
    Delete item
</h2>

<?php // The form posts to the same URL: the controller deletes on POST only. ?>
<form enctype="multipart/form-data" method="post"
    action="<?=$view->commonView->currentUrl?>">
    <input type="hidden" name="<?=CsrfTokenServiceInterface::FIELD_NAME?>" value="<?=$view->csrfToken?>">

    <p>
        Delete [<?=$view->itemEntity->id?>] <?=$view->escape($view->itemEntity->item->name)?>?
        <?php if ($view->itemEntity->totalItems > 0) { ?>
            It contains <?=$view->itemEntity->totalItems?> item(s).
        <?php } ?>
    </p>

    <fieldset>
        <button type="submit">Delete</button>
        <p>
            <?php // Cancel: back to the item. ?>
            <a role="button" href="<?=$routeUrl?>item/<?=$view->itemEntity->id?>" title="Cancel">Cancel</a>
        </p>
    </fieldset>

</form>
