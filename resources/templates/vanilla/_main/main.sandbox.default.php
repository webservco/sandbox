<?php

declare(strict_types=1);

use Project\View\Sandbox\MainView;
use WebServCo\View\Contract\HTMLRendererInterface;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
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
<html lang="<?=$view->languageCode()?>">
    <?php // An example of rendering a partial template ?>
    <?=$this->renderView($view, sprintf($partTpl, 'head'))?>
    <body>
        <nav aria-label="<?=$view->text('sandbox.layout.language')?>">
            <ul>
                <?php foreach ($view->languageLinks as $languageLink) { ?>
                <li>
                    <?php if ($languageLink->isCurrent) { ?>
                    <strong
                        lang="<?=$languageLink->code?>"
                        aria-current="true"
                    ><?=$view->escape($languageLink->name)?></strong>
                    <?php } else { ?>
                    <a
                        href="<?=$view->escape($languageLink->url)?>"
                        hreflang="<?=$languageLink->code?>"
                        lang="<?=$languageLink->code?>"
                    ><?=$view->escape($languageLink->name)?></a>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
        </nav>

        <h1><?=$view->text('sandbox.layout.hello')?></h1>

        <p><?=$view->text('sandbox.layout.base_url')?> <?=$view->commonView->baseUrl?></p>

        <?=$view->data?>
    </body>
</html>
