<?php

declare(strict_types=1);

use Project\Controller\Service\API\APIController;
use Project\Factory\View\Container\API\APIViewContainerFactory;

return [
    // Use general API Controller and View factory.
    'about' => [APIController::class, APIViewContainerFactory::class],
    // Use general API Controller and View factory.
    'version' => [APIController::class, APIViewContainerFactory::class],
];
