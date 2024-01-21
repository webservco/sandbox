<?php

declare(strict_types=1);

use Project\View\Stuff\AuthenticationView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof AuthenticationView);
?>
<h2>
    Authentication
</h2>

<form enctype="multipart/form-data" method="post"
    action="<?=$view->commonView->currentUrl?>">

    <fieldset>
        <label for="<?=$view->form->getField('password')->getId()?>">
            <?=$view->form->getField('password')->getTitle()?>
        </label>
        <input type="password"
            id="<?=$view->form->getField('password')->getId()?>"
            name="<?=$view->form->getField('password')->getName()?>"
            placeholder="<?=$view->form->getField('password')->getPlaceholder()?>"
            value="<?=$view->escape($view->form->getField('password')->getValue())?>"
            <?=$view->form->getField('password')->isRequired()
            ? ' required'
            : ''?>
            <?=$view->form->getField('password')->getErrorMessages() !== []
            ? 'aria-invalid="true"'
            : ''?>>
        <?php if ($view->form->getField('password')->getErrorMessages() !== []) { ?>
            <small>
                <mark>
                    <?=implode('<br>', $view->form->getField('password')->getErrorMessages())?>
                </mark>
            </small>
        <?php } ?>
    </fieldset>

    <fieldset>
        <button type="submit">Submit</button>
    </fieldset>

</form>
