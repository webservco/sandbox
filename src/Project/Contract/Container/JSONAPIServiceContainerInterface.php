<?php

declare(strict_types=1);

namespace Project\Contract\Container;

use Project\Contract\Factory\API\JSONAPIHandlerFactoryInterface;
use WebServCo\JSONAPI\Contract\Service\JSONAPIRequestServiceInterface;

interface JSONAPIServiceContainerInterface
{
    public function getDefaultHandlerFactory(): JSONAPIHandlerFactoryInterface;

    public function getJsonApiRequestService(): JSONAPIRequestServiceInterface;
}
