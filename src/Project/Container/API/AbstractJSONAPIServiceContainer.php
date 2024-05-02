<?php

declare(strict_types=1);

namespace Project\Container\API;

use Project\Contract\Container\API\JSONAPIServiceContainerInterface;
use Project\Contract\Factory\API\JSONAPIHandlerFactoryInterface;
use Project\Factory\Handler\JSONAPIDefaultHandlerFactory;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Http\Service\Message\Request\RequestBodyService;
use WebServCo\Http\Service\Message\Request\RequestHeaderService;
use WebServCo\JSONAPI\Contract\Service\JSONAPIRequestServiceInterface;
use WebServCo\JSONAPI\Service\JSONAPIRequestService;

abstract class AbstractJSONAPIServiceContainer implements JSONAPIServiceContainerInterface
{
    private ?JSONAPIHandlerFactoryInterface $defaultItemHandlerFactory = null;

    private ?JSONAPIRequestServiceInterface $jsonApiRequestService = null;

    public function __construct(protected DataExtractionContainerInterface $dataExtractionContainer)
    {
    }

    public function getDefaultHandlerFactory(): JSONAPIHandlerFactoryInterface
    {
        if ($this->defaultItemHandlerFactory === null) {
            $this->defaultItemHandlerFactory = new JSONAPIDefaultHandlerFactory(
                $this->dataExtractionContainer,
                $this->getJsonApiRequestService(),
            );
        }

        return $this->defaultItemHandlerFactory;
    }

    public function getJsonApiRequestService(): JSONAPIRequestServiceInterface
    {
        if ($this->jsonApiRequestService === null) {
            $this->jsonApiRequestService = new JSONAPIRequestService(
                $this->dataExtractionContainer,
                new RequestBodyService(),
                new RequestHeaderService(),
            );
        }

        return $this->jsonApiRequestService;
    }
}
