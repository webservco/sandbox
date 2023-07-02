<?php

declare(strict_types=1);

use Project\View\Sandbox\TestView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof TestView);
?>
<div>
    String property (user input): <?=$view->escape($view->stringProperty)?>
    <br>
    Int property: <?=$view->intProperty?>
</div>
