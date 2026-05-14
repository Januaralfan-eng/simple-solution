<?php

namespace App\Exceptions;

use RuntimeException;

class ContactException extends RuntimeException
{
    public static function creationFailed(string $reason): self
    {
        return new self("Contact creation failed: {$reason}", 500);
    }
}
