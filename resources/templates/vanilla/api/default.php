<?php

declare(strict_types=1);

use Project\View\API\APIView;

// @phan-suppress-next-line PhanImpossibleConditionInGlobalScope, PhanRedundantConditionInGlobalScope
assert(isset($view) && $view instanceof APIView);

/**
 * No content, API only uses the main template;
 * However this file musy be present if HTMLRendererInterface is available for this page.
 *
 * @see ApiRequestHandler.getAvailableViewRenderers()
 */
