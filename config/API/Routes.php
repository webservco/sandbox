<?php

declare(strict_types=1);

use Project\Controller\API\APIController;

return [
    // Use general API Controller and View factory.
    'v1/about' => APIController::class,
    // Use general API Controller and View factory.
    'v1/version' => APIController::class,
];
