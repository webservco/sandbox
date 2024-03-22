<?php

declare(strict_types=1);

namespace Project\View\API;

use WebServCo\JSONAPI\Contract\Document\DataInterface;
use WebServCo\JSONAPI\Contract\Document\ErrorsInterface;
use WebServCo\JSONAPI\Contract\Document\JsonapiInterface;
use WebServCo\JSONAPI\Contract\Document\MetaInterface;
use WebServCo\View\AbstractView;
use WebServCo\View\Contract\ViewInterface;

final class ItemView extends AbstractView implements ViewInterface
{
    public function __construct(
        // jsonapi is optional
        public readonly ?JsonapiInterface $jsonapi,
        // data and errors must no co-exist
        public readonly ?DataInterface $data,
        // data and errors must no co-exist
        public readonly ?ErrorsInterface $errors,
        // meta is optional
        public readonly ?MetaInterface $meta,
    ) {
    }
}
