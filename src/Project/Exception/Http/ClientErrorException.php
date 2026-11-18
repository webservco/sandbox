<?php

declare(strict_types=1);

namespace Project\Exception\Http;

use OutOfRangeException;
use RuntimeException;

/**
 * A client error (4xx) whose page tells the user what happened, in every environment: `ErrorController` shows its
 * code and message. Any other throwable gets the generic error page outside development.
 *
 * For example the CSRF middleware's error: 403, the page has expired.
 */
final class ClientErrorException extends RuntimeException
{
    public function __construct(int $code, string $message)
    {
        if ($code < 400 || $code > 499) {
            throw new OutOfRangeException('Not a client error code.');
        }

        parent::__construct($message, $code);
    }
}
