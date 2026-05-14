<?php

namespace App\Exceptions;

use RuntimeException;

class PortfolioException extends RuntimeException
{
    public static function likeFailed(string $reason): self
    {
        return new self("Portfolio like failed: {$reason}", 500);
    }

    public static function notFound(string $slug): self
    {
        return new self("Project '{$slug}' not found.", 404);
    }
}
