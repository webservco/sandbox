<?php

declare(strict_types=1);

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
use WebServCo\JSONAPI\View\ItemView;

assert(isset($view) && $view instanceof ItemView);

/**
 * No content, API only uses the main template;
 * However this file must be present if HTMLRendererInterface is available for this page.
 *
 * @see ApiRequestHandler.getAvailableViewRenderers()
 */
