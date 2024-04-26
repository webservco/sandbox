<?php

declare(strict_types=1);

namespace Project\Contract\Factory\API;

use WebServCo\JSONAPI\Contract\Service\JSONAPIHandlerInterface;

interface JSONAPIHandlerFactoryInterface
{
    public function createHandler(): JSONAPIHandlerInterface;
}
