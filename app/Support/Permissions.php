<?php

namespace App\Support;

final class Permissions
{
    public const PROMO_READ = 'promo.read';

    public const PROMO_CREATE = 'promo.create';

    public const PROMO_UPDATE = 'promo.update';

    public const PROMO_DELETE = 'promo.delete';

    public const TICKET_READ = 'ticket.read';

    public const TICKET_UPDATE = 'ticket.update';

    public const TICKET_CLOSE = 'ticket.close';

    public const LINE_READ = 'line.read';

    public const LINE_EDIT = 'line.edit';

    public const AGENT_CREATE = 'agent.create';

    public const AGENT_READ = 'agent.read';

    public const AGENT_ASSIGN = 'agent.assign';

    public const AGENT_UPDATE = 'agent.update';

    public const AGENT_PERMISSIONS = 'agent.permissions';

    public const BONO_READ = 'bono.read';

    public const BONO_CREATE = 'bono.create';

    public const BONO_UPDATE = 'bono.update';

    public const BONO_DELETE = 'bono.delete';

    public const SORTEO_READ = 'sorteo.read';

    public const SORTEO_CREATE = 'sorteo.create';

    public const SORTEO_UPDATE = 'sorteo.update';

    public const SORTEO_DELETE = 'sorteo.delete';

    public const NEWS_READ = 'news.read';

    public const NEWS_CREATE = 'news.create';

    public const NEWS_UPDATE = 'news.update';

    public const NEWS_DELETE = 'news.delete';

    public const USER_READ = 'user.read';

    public const USER_UPDATE = 'user.update';

    public const USER_BLOCK = 'user.block';

    public const PLATFORM_READ = 'platform.read';

    public const PLATFORM_CREATE = 'platform.create';

    public const PLATFORM_UPDATE = 'platform.update';

    public const PLATFORM_DELETE = 'platform.delete';

    public const HOME_EDIT = 'home.edit';

    /**
     * Returns [permission => [icon_class, human_label]] for all permissions.
     */
    public static function labels(): array
    {
        return [
            // Promociones
            self::PROMO_READ => ['fa-solid fa-tags',             'Ver promociones'],
            self::PROMO_CREATE => ['fa-solid fa-tag',              'Crear promociones'],
            self::PROMO_UPDATE => ['fa-solid fa-pen-to-square',    'Editar promociones'],
            self::PROMO_DELETE => ['fa-solid fa-trash',            'Eliminar promociones'],
            // Tickets
            self::TICKET_READ => ['fa-solid fa-ticket',           'Ver tickets'],
            self::TICKET_UPDATE => ['fa-solid fa-ticket-simple',    'Editar tickets'],
            self::TICKET_CLOSE => ['fa-solid fa-circle-xmark',     'Cerrar tickets'],
            // Líneas
            self::LINE_READ => ['fa-solid fa-list',             'Ver línea'],
            self::LINE_EDIT => ['fa-solid fa-sliders',          'Editar línea'],
            // Agentes
            self::AGENT_CREATE => ['fa-solid fa-user-plus',        'Crear agentes'],
            self::AGENT_ASSIGN => ['fa-solid fa-user-group',       'Asignar agentes'],
            self::AGENT_UPDATE => ['fa-solid fa-user-pen',         'Editar agentes'],
            self::AGENT_PERMISSIONS => ['fa-solid fa-shield-halved',    'Gestionar permisos'],
            // Bonos
            self::BONO_READ => ['fa-solid fa-gift',             'Ver bonos'],
            self::BONO_CREATE => ['fa-solid fa-circle-plus',      'Crear bonos'],
            self::BONO_UPDATE => ['fa-solid fa-pen-to-square',    'Editar bonos'],
            self::BONO_DELETE => ['fa-solid fa-trash',            'Eliminar bonos'],
            // Sorteos
            self::SORTEO_READ => ['fa-solid fa-dice',             'Ver sorteos'],
            self::SORTEO_CREATE => ['fa-solid fa-dice-d6',          'Crear sorteos'],
            self::SORTEO_UPDATE => ['fa-solid fa-pen-to-square',    'Editar sorteos'],
            self::SORTEO_DELETE => ['fa-solid fa-trash',            'Eliminar sorteos'],
            // Novedades / Blog
            self::NEWS_READ => ['fa-solid fa-newspaper',        'Ver novedades'],
            self::NEWS_CREATE => ['fa-solid fa-file-circle-plus', 'Crear novedades'],
            self::NEWS_UPDATE => ['fa-solid fa-pen-to-square',    'Editar novedades'],
            self::NEWS_DELETE => ['fa-solid fa-trash',            'Eliminar novedades'],
            // Clientes
            self::USER_READ => ['fa-solid fa-users',            'Ver clientes'],
            self::USER_UPDATE => ['fa-solid fa-user-pen',         'Editar clientes'],
            self::USER_BLOCK => ['fa-solid fa-user-slash',       'Bloquear clientes'],
            // Home
            self::HOME_EDIT => ['fa-solid fa-house-chimney',    'Editar home'],
        ];
    }

    public static function catalog(): array
    {
        return [
            'promo' => [self::PROMO_READ, self::PROMO_CREATE, self::PROMO_UPDATE, self::PROMO_DELETE],
            'ticket' => [self::TICKET_READ, self::TICKET_UPDATE, self::TICKET_CLOSE],
            'line' => [self::LINE_READ, self::LINE_EDIT],
            'agent' => [self::AGENT_CREATE, self::AGENT_READ, self::AGENT_ASSIGN, self::AGENT_UPDATE, self::AGENT_PERMISSIONS],
            'bono' => [self::BONO_READ, self::BONO_CREATE, self::BONO_UPDATE, self::BONO_DELETE],
            'sorteo' => [self::SORTEO_READ, self::SORTEO_CREATE, self::SORTEO_UPDATE, self::SORTEO_DELETE],
            'news' => [self::NEWS_READ, self::NEWS_CREATE, self::NEWS_UPDATE, self::NEWS_DELETE],
            'user' => [self::USER_READ, self::USER_UPDATE, self::USER_BLOCK],
            'home' => [self::HOME_EDIT],
        ];
    }

    public static function all(): array
    {
        return array_values(array_merge(...array_values(self::catalog())));
    }
}
