<?php

declare(strict_types=1);

use WebServCo\View\View\MainView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof MainView);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>main.error.default</title>
    <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgo=">
</head>
<body>
<h1>Error!</h1>

<p>baseUrl: <?=$view->commonView->baseUrl?></p>

<?=$view->data?>
</body>
</html>
