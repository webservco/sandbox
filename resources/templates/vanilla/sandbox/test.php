<?php

declare(strict_types=1);

use Project\View\Sandbox\TestView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof TestView);
?>
<dl>
    <dt><?=$view->text('sandbox.test.string_property')?></dt>
    <dd><?=$view->escape($view->stringProperty)?></dd>
    <dt><?=$view->text('sandbox.test.int_property')?></dt>
    <dd><?=$view->intProperty?></dd>
    <dt><?=$view->text('sandbox.test.date')?></dt>
    <dd><?=$view->date($view->dateTime)?></dd>
    <dt><?=$view->text('sandbox.test.number')?></dt>
    <dd><?=$view->number($view->number, 2)?></dd>
    <dt><?=$view->text('sandbox.test.plural')?></dt>
    <?php foreach ($view->counts as $count) { ?>
    <dd><?=$view->text('sandbox.test.items', ['count' => $count])?></dd>
    <?php } ?>
</dl>
