<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

final class UserFacingException extends RuntimeException
{
    public function __construct(
        public readonly string $title,
        public readonly string $publicMessage,
        public readonly string $recommendedAction,
        public readonly int $status = 409,
        ?Throwable $previous = null,
    ) {
        parent::__construct($publicMessage, previous: $previous);
    }
}
