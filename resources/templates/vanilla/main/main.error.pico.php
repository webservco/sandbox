<?php

declare(strict_types=1);

use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\View\MainView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof MainView);

$routeUrl = sprintf('%s%s/', $view->commonView->baseUrl, RouteInterface::ROUTE);
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Error</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@next/css/pico.classless.min.css">
        <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgo=">
    </head>
    <body>
        <header>
            <nav>
                <ul>
                    <li><strong><a href="<?=$view->commonView->baseUrl?>">Home</a></strong></li>
                </ul>
                <ul>
                    <li><a href="<?=$routeUrl?>logout">Logout</a></li>
                </ul>
            </nav>
            <hr>
        </header>

        <main>
            <?=$view->data?>
        </main>

        <footer>
            <hr>
        </footer>
    </body>
</html>
