<?php

declare(strict_types=1);

use Project\View\Error\ErrorView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof ErrorView);
?>
<div>
    <h2>Custom error page.</h2>

    <p>Code: <?=$view->code?></p>
    <p>Message: <?=$view->escape($view->message)?></p>
</div>
