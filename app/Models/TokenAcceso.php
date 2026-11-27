<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;

final class TokenAcceso extends PersonalAccessToken
{
    protected $table = 'personal_access_tokens';

    protected static function booted(): void
    {
        static::creating(function (self $token): void {
            $token->id_empresa = $token->tokenable?->id_empresa;
        });
    }
}
