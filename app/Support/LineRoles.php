<?php

namespace App\Support;

final class LineRoles
{
    public const ENCARGADO = 'encargado';
    public const MIEMBRO = 'miembro';

    public static function all(): array
    {
        return [
            self::ENCARGADO,
            self::MIEMBRO,
        ];
    }
}
