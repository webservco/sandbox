<?php

declare(strict_types=1);

use WebServCo\View\MainView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof MainView);
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>main.stuff.default</title>
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
                <strong><a href="<?=$view->commonView->baseUrl?>">Home</a></strong>
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
