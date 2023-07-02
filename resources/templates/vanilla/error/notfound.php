<?php

declare(strict_types=1);

use Project\View\Error\NotFoundView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof NotFoundView);
?>
<div>
    <h2>Custom 404 page.</h2>

    <p>Method: <?=$view->escape($view->method)?></p>
    <p>Uri: <?=$view->escape($view->uri)?></p>
    <p>User agent: <?=$view->escape($view->userAgent)?></p>
</div>
