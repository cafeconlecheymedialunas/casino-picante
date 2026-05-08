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
    public const LINE_VIEW = 'line.view';
    public const LINE_CREATE = 'line.create';
    public const LINE_EDIT = 'line.edit';

    public const AGENT_CREATE = 'agent.create';
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

    public static function catalog(): array
    {
        return [
            'promo' => [self::PROMO_READ, self::PROMO_CREATE, self::PROMO_UPDATE, self::PROMO_DELETE],
            'ticket' => [self::TICKET_READ, self::TICKET_UPDATE, self::TICKET_CLOSE],
            'line' => [self::LINE_READ, self::LINE_VIEW, self::LINE_CREATE, self::LINE_EDIT],
            'agent' => [self::AGENT_CREATE, self::AGENT_ASSIGN, self::AGENT_UPDATE, self::AGENT_PERMISSIONS],
            'bono' => [self::BONO_READ, self::BONO_CREATE, self::BONO_UPDATE, self::BONO_DELETE],
            'sorteo' => [self::SORTEO_READ, self::SORTEO_CREATE, self::SORTEO_UPDATE, self::SORTEO_DELETE],
            'news' => [self::NEWS_READ, self::NEWS_CREATE, self::NEWS_UPDATE, self::NEWS_DELETE],
            'user' => [self::USER_READ, self::USER_UPDATE, self::USER_BLOCK],
            'platform' => [self::PLATFORM_READ, self::PLATFORM_CREATE, self::PLATFORM_UPDATE, self::PLATFORM_DELETE],
            'home' => [self::HOME_EDIT],
        ];
    }

    public static function all(): array
    {
        return array_values(array_merge(...array_values(self::catalog())));
    }
}
