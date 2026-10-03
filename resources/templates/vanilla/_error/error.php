<?php

declare(strict_types=1);

use Project\View\Error\ErrorView;

assert(isset($view) && $view instanceof ErrorView);
?>
<div>
    <h2>Error</h2>

    <p>Code: <?=$view->escape($view->code)?></p>
    <p>Message: <?=$view->escape($view->message)?></p>
</div>
