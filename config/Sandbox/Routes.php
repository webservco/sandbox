<?php

declare(strict_types=1);

use Project\Controller\Service\Sandbox\TestController;
use Project\Factory\View\Container\Sandbox\TestViewContainerFactory;

// Routes configuration.
// Since this is not a class we keep it separate from the `src` directory.
// Not adding namespace declaration (Psalm dead code error).
// Not using docblock here to prevent "The file-level docblock must follow the opening PHP tag in the file header"
return [
    'test' => [TestController::class, TestViewContainerFactory::class],
];