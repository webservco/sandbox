<?php

declare(strict_types=1);

use Project\Controller\Stuff\AuthenticationController;
use Project\Controller\Stuff\ItemController;
use Project\Controller\Stuff\ItemDeleteController;
use Project\Controller\Stuff\ItemsController;
use Project\Controller\Stuff\LogoutController;
use Project\Controller\Stuff\SearchItemController;

// Routes configuration.
// Since this is not a class we keep it separate from the `src` directory.
// Not adding namespace declaration (Psalm dead code error).
// Not using docblock here to prevent "The file-level docblock must follow the opening PHP tag in the file header"
return [
    'authenticate' => AuthenticationController::class,
    'item' => ItemController::class,
    'item-delete' => ItemDeleteController::class,
    'items' => ItemsController::class,
    'logout' => LogoutController::class,
    'search_item' => SearchItemController::class,
];
