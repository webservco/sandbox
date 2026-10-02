<?php

declare(strict_types=1);

use WebServCo\JSONAPI\View\ItemView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof ItemView);

/**
 * No content, API only uses the main template;
 * However this file must be present if HTMLRendererInterface is available for this page.
 *
 * @see ApiRequestHandler.getAvailableViewRenderers()
 */
