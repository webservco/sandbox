<?php

declare(strict_types=1);

use Project\View\API\MainView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof MainView);
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>main.api.default</title>
        <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgo=">
    </head>
    <body>
        <h1>Welcome to the API.</h1>

        <p>Please use the appropriate "Accept" headers in order to use the API.</p>
    </body>
</html>
