<?php

declare(strict_types=1);

use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\View\View\MainView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof MainView);

$routeUrl = sprintf('%s%s/', $view->commonView->baseUrl, RouteInterface::ROUTE);
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Stuff</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@next/css/pico.classless.min.css">
        <?php
        /**
         * Avoid using the base tag;
         * "Links pointing to a fragment in the document — e.g. <a href="#some-id">
         * — are resolved with the <base>, triggering an HTTP request
         * to the base URL with the fragment attached."
         * https://developer.mozilla.org/en-US/docs/Web/HTML/Element/base
         */
        ?>
        <?php
        /**
         * Prevent separate favicon request.
         * Source: https://stackoverflow.com/a/13416784/14583382
         */
        ?>
        <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgo=">
    </head>
    <body>
        <header>
            <nav>
                <ul>
                    <li><strong><a href="<?=$routeUrl?>items">Home</a></strong></li>
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
            <form enctype="multipart/form-data" method="post"
                action="<?=$routeUrl?>search_item">
                <input type="search" id="search_item" name="search_item" placeholder="Search">
            </form>
        </footer>
    </body>
</html>
