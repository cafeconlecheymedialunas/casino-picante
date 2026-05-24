<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    public const DEFAULT_LOGO = '';

    public const DEFAULT_FAVICON = 'favicon.ico';

    public const DEFAULT_SITE_TITLE = 'RED PICANTES';

    protected $table = 'settings';

    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = static::query()->firstOrCreate(
            ['key' => $key],
            ['value' => $default]
        );

        return $setting->value ?: $default;
    }

    public static function assetUrl(?string $path, ?string $fallback = null): string
    {
        $path = $path ?: $fallback ?: '';

        if ($path === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        if ($path === static::DEFAULT_FAVICON) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    public static function siteLogoPath(): string
    {
        return static::getValue('site_logo', static::DEFAULT_LOGO) ?: '';
    }

    public static function siteLogoUrl(): string
    {
        return static::assetUrl(static::siteLogoPath());
    }

    public static function siteFaviconPath(): string
    {
        return static::getValue('site_favicon', static::DEFAULT_FAVICON) ?: static::DEFAULT_FAVICON;
    }

    public static function siteFaviconUrl(): string
    {
        return static::assetUrl(static::siteFaviconPath(), static::DEFAULT_FAVICON);
    }

    public static function siteTitle(): string
    {
        return static::getValue('site_title', static::DEFAULT_SITE_TITLE) ?: static::DEFAULT_SITE_TITLE;
    }
}
