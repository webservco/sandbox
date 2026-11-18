<?php

declare(strict_types=1);

namespace Project\Container\Stuff;

use Override;
use Project\Contract\Container\Stuff\FormFactoryContainerInterface;
use Project\Contract\Container\Stuff\StuffLocalServiceContainerInterface;
use WebServCo\Configuration\Contract\ConfigurationGetterInterface;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Database\Contract\PDOContainerInterface;
use WebServCo\Session\Contract\CsrfTokenServiceInterface;
use WebServCo\Session\Contract\FlashMessageServiceInterface;
use WebServCo\Session\Contract\SessionStoreInterface;
use WebServCo\Session\Service\CsrfTokenService;
use WebServCo\Session\Service\FlashMessageService;
use WebServCo\Stuff\Container\Storage\StuffStorageContainer;
use WebServCo\Stuff\Contract\Container\Storage\StuffStorageContainerInterface;

final class LocalServiceContainer implements StuffLocalServiceContainerInterface
{
    private ?CsrfTokenServiceInterface $csrfTokenService = null;
    private ?FlashMessageServiceInterface $flashMessageService = null;
    private ?FormFactoryContainerInterface $formFactoryContainer = null;
    private ?StuffStorageContainerInterface $storageContainer = null;

    public function __construct(
        private ConfigurationGetterInterface $configurationGetter,
        private DataExtractionContainerInterface $dataExtractionContainer,
        private PDOContainerInterface $pdoContainer,
        private SessionStoreInterface $sessionStore,
    ) {
    }

    #[Override]
    public function getCsrfTokenService(): CsrfTokenServiceInterface
    {
        if ($this->csrfTokenService === null) {
            $this->csrfTokenService = new CsrfTokenService($this->sessionStore);
        }

        return $this->csrfTokenService;
    }

    #[Override]
    public function getFlashMessageService(): FlashMessageServiceInterface
    {
        if ($this->flashMessageService === null) {
            $this->flashMessageService = new FlashMessageService($this->sessionStore);
        }

        return $this->flashMessageService;
    }

    #[Override]
    public function getFormFactoryContainer(): FormFactoryContainerInterface
    {
        if ($this->formFactoryContainer === null) {
            $this->formFactoryContainer = new FormFactoryContainer($this->configurationGetter);
        }

        return $this->formFactoryContainer;
    }

    #[Override]
    public function getStorageContainer(): StuffStorageContainerInterface
    {
        if ($this->storageContainer === null) {
            $this->storageContainer = new StuffStorageContainer($this->dataExtractionContainer, $this->pdoContainer);
        }

        return $this->storageContainer;
    }
}
