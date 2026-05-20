<?php

namespace App\Support;

final class Roles
{
    public const ADMIN = 'admin';
    public const AGENTE = 'agente';
    public const CLIENTE = 'cliente';
    public const CAJERO = 'cajero';

    public static function all(): array
    {
        return [
            self::ADMIN,
            self::AGENTE,
            self::CLIENTE,
            self::CAJERO,
        ];
    }
}
