<?php

namespace App\Exceptions;

use Exception;

class TokenRequestFailedException extends Exception
{
    public function __construct(
        private readonly int $status,
        private readonly ?string $error,
        private readonly ?string $errorDescription,
    ) {
        parent::__construct("Passport token request failed with status {$status}");
    }

    public function context(): array
    {
        return [
            'status' => $this->status,
            'error' => $this->error,
            'error_description' => $this->errorDescription,
        ];
    }

    public function isInvalidCredentials(): bool
    {
        return $this->error === 'invalid_grant';
    }
}
