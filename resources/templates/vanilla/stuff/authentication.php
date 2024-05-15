<?php

declare(strict_types=1);

use Fig\Http\Message\StatusCodeInterface;
use Project\View\Stuff\AuthenticationView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof AuthenticationView);

$displayErrors = [];
if ($view->form->getErrors() !== []) {
    foreach ($view->form->getErrors() as $error) {
        if ($error->getCode() === StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED) {
            continue;
        }

        $displayErrors[] = $error->getMessage();
    }
}
?>
<h2>
    Authentication
</h2>

<form enctype="multipart/form-data" method="post"
    action="<?=$view->commonView->currentUrl?>">

    <?php if ($displayErrors !== []) { ?>
        <p>
            <mark>
                <?=implode('<br>', $displayErrors)?>
            </mark>
        </p>
    <?php } ?>

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
            <?=$view->form->getField('password')->getErrors() !== []
            ? 'aria-invalid="true"'
            : ''?>>
        <?php if ($view->form->getField('password')->getErrors() !== []) { ?>
            <small>
                <mark>
                    <?php foreach ($view->form->getField('password')->getErrors() as $index => $error) { ?>
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
    </fieldset>

</form>
