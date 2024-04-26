<?php

declare(strict_types=1);

namespace Project\Container\API;

use Project\Contract\Container\API\APIJSONAPIServiceContainerInterface;
use Project\Contract\Container\API\APILocalServiceContainerInterface;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;

final class APILocalServiceContainer implements APILocalServiceContainerInterface
{
    private ?APIJSONAPIServiceContainerInterface $jsonApiServiceContainer = null;

    public function __construct(private DataExtractionContainerInterface $dataExtractionContainer)
    {
    }

    public function getJsonApiServiceContainer(): APIJSONAPIServiceContainerInterface
    {
        if ($this->jsonApiServiceContainer === null) {
            $this->jsonApiServiceContainer = new APIJSONAPIServiceContainer($this->dataExtractionContainer);
        }

        return $this->jsonApiServiceContainer;
    }
}
