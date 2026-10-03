<?php

declare(strict_types=1);

use WebServCo\View\Contract\HTMLRendererInterface;
use WebServCo\View\View\MainView;

// @phan-suppress-next-line PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof MainView);

/**
 * `$this` is the renderer (the template is required from inside it); used to render partial templates.
 *
 * Typing of `$this`:
 * - Psalm: `@psalm-scope-this` (requires a class; an `assert` alone does not work);
 * - PHPStan: `@phpstan-var` (an `assert` alone requires `isset($this)`, which Psalm reports as redundant);
 * - Phan: can not type `$this` outside a class;
 * - IDE (eg. PhpStorm, autocomplete): the `assert` below (it does not read the tags above).
 *
 * @psalm-scope-this \WebServCo\View\Service\HTMLRenderer
 * @phpstan-var \WebServCo\View\Contract\HTMLRendererInterface $this
 * @phan-file-suppress PhanUndeclaredThis
 */
assert($this instanceof HTMLRendererInterface);

$partTpl = __DIR__ . '/../_partial/%s.php';
?>
<!doctype html>
<html lang="en">
    <?php // An example of rendering a partial template ?>
    <?=$this->renderView($view->commonView, sprintf($partTpl, 'head'))?>
    <body>
        <h1>Hello, world!</h1>

        <p>baseUrl: <?=$view->commonView->baseUrl?></p>

        <?=$view->data?>
    </body>
</html>
