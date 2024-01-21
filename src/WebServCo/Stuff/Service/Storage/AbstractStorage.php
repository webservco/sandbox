<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Service\Storage;

use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Database\Contract\PDOContainerInterface;

abstract class AbstractStorage
{
    public function __construct(
        protected DataExtractionContainerInterface $dataExtractionContainer,
        protected PDOContainerInterface $pdoContainer,
    ) {
    }
}
