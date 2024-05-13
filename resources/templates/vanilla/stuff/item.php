<?php

declare(strict_types=1);

use Project\View\Stuff\ItemView;
use WebServCo\Stuff\Contract\RouteInterface;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof ItemView);

$routeUrl = sprintf('%s%s/', $view->commonView->baseUrl, RouteInterface::ROUTE);
$backUrl = sprintf(
    '%sitems%s',
    $routeUrl,
    /**
     * "PhanPossiblyUndeclaredProperty Reference to possibly undeclared property id of expression of type
     * ?\WebServCo\Stuff\Entity\Item\ItemEntity (null does not declare that property)"
     */
    $view->itemEntity !== null ? sprintf('/%d', $view->itemEntity->id) : '',
);
?>
<h2>
    Item
    <?php if ($view->itemEntity !== null) { ?>
        <small><small><small><small><small>
            (I<?=$view->itemEntity->id?>)
        </small></small></small></small></small>
    <?php } ?>
</h2>

<form enctype="multipart/form-data" method="post"
    action="<?=$view->commonView->currentUrl?>">

    <?php if ($view->form->getErrors() !== []) { ?>
        <p>
            <mark>
                <?php foreach ($view->form->getErrors() as $index => $error) { ?>
                    <?php if ($index > 0) { ?>
                        <br>
                    <?php } ?>
                    <?=$error->getMessage()?>
                <?php } ?>
            </mark>
        </p>
    <?php } ?>

    <fieldset>
        <label for="<?=$view->form->getField('name')->getId()?>">
            <?=$view->form->getField('name')->getTitle()?>
        </label>
        <input type="text"
            id="<?=$view->form->getField('name')->getId()?>"
            name="<?=$view->form->getField('name')->getName()?>"
            placeholder="<?=$view->form->getField('name')->getPlaceholder()?>"
            value="<?=$view->escape($view->form->getField('name')->getValue())?>"
            <?=$view->form->getField('name')->isRequired()
            ? ' required'
            : ''?>
            <?=$view->form->getField('name')->getErrors() !== []
            ? 'aria-invalid="true"'
            : ''?>>
        <?php if ($view->form->getField('name')->getErrors() !== []) { ?>
            <small>
                <mark>
                    <?php foreach ($view->form->getField('name')->getErrors() as $index => $error) { ?>
                        <?php if ($index > 0) { ?>
                            <br>
                        <?php } ?>
                        <?=$error->getMessage()?>
                    <?php } ?>
                </mark>
            </small>
        <?php } ?>
    </fieldset>

    <fieldset>
        <label for="<?=$view->form->getField('description')->getId()?>">
            <?=$view->form->getField('description')->getTitle()?>
        </label>
        <input type="text"
            id="<?=$view->form->getField('description')->getId()?>"
            name="<?=$view->form->getField('description')->getName()?>"
            placeholder="<?=$view->form->getField('description')->getPlaceholder()?>"
            value="<?=$view->escape($view->form->getField('description')->getValue())?>"
            <?=$view->form->getField('description')->isRequired()
            ? ' required'
            : ''?>
            <?=$view->form->getField('description')->getErrors() !== []
            ? 'aria-invalid="true"'
            : ''?>>
        <?php if ($view->form->getField('description')->getErrors() !== []) { ?>
            <small>
                <mark>
                    <?php foreach ($view->form->getField('description')->getErrors() as $index => $error) { ?>
                        <?php if ($index > 0) { ?>
                            <br>
                        <?php } ?>
                        <?=$error->getMessage()?>
                    <?php } ?>
                </mark>
            </small>
        <?php } ?>
    </fieldset>

    <fieldset>
        <button type="submit">Submit</button>
        <?php
        // When editing and existing item.
        if ($view->itemEntity !== null) {
            $itemDeleteUrl = sprintf(
                '%sitem-delete/%d%s',
                $routeUrl,
                $view->itemEntity->id,
                $view->itemEntity->parentItemId !== null
                    ? sprintf('?parent=%d', $view->itemEntity->parentItemId)
                    : '',
            );
            ?>
            <p>
                <a role="button" href="<?=$itemDeleteUrl?>" title="Delete item">
                    Delete item (direct action, no confirmation asked!)
                </a>
            </p>
        <?php } ?>
        <p>
            <?php // Go back to list of items ?>
            <a role="button" href="<?=$backUrl?>" title="Back">Back</a>
        </p>
    </fieldset>

</form>
