<?php

declare(strict_types=1);

namespace App\Models;

final class AccessToken
{
    private string $accessToken;
    private int $expiresAt;
    private string $refreshToken;

    public function __construct(string $accessToken, string $refreshToken, int $expiresAt)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresAt = $expiresAt;
    }

    public function __toString(): string
    {
        return $this->getAccessToken();
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
