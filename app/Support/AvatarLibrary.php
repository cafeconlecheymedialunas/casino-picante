<?php

namespace App\Support;

class AvatarLibrary
{
    private const PREFIX = 'avatar_';

    public static function options(?string $selected = null, ?int $limit = null): array
    {
        $options = [];
        $seeds = self::seeds();
        if ($limit !== null) {
            $seeds = array_slice($seeds, 0, max(1, $limit));
        }

        foreach (self::styles() as $style) {
            foreach ($seeds as $index => $seed) {
                $value = self::value($style, $seed);
                $options[$value] = [
                    'value' => $value,
                    'label' => self::label($style).' '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'seed' => $seed,
                    'style' => $style,
                    'url' => self::url($value),
                ];
            }
        }

        $selected = $selected ?: self::default();
        if (! isset($options[$selected]) && self::isValid($selected)) {
            [$style, $seed] = self::parts($selected);
            $options = [$selected => [
                'value' => $selected,
                'label' => 'Actual',
                'seed' => $seed,
                'style' => $style,
                'url' => self::url($selected),
            ]] + $options;
        }

        return array_values($options);
    }

    public static function isValid(?string $avatar): bool
    {
        return is_string($avatar) && preg_match('/^avatar_[A-Za-z0-9_-]{1,80}$/', $avatar) === 1;
    }

    public static function default(): string
    {
        return self::value('adventurer', 'red-picantes-01');
    }

    public static function seed(?string $avatar): string
    {
        return self::parts($avatar)[1];
    }

    public static function style(?string $avatar): string
    {
        return self::parts($avatar)[0];
    }

    public static function url(?string $avatar): string
    {
        return self::remoteUrl($avatar);
    }

    public static function remoteUrl(?string $avatar): string
    {
        [$style, $seed] = self::parts($avatar);

        $config = self::config();

        return 'https://api.dicebear.com/'.$config['version'].'/'.$style.'/svg?seed='.urlencode($seed).'&backgroundColor='.$config['backgroundColor'];
    }

    public static function styles(): array
    {
        return self::config()['styles'];
    }

    public static function seeds(): array
    {
        return self::config()['seeds'];
    }

    public static function valueFor(string $style, string $seed): string
    {
        return self::value($style, $seed);
    }

    private static function value(string $style, string $seed): string
    {
        return self::PREFIX.$style.'__'.$seed;
    }

    private static function parts(?string $avatar): array
    {
        $avatar = self::isValid($avatar) ? $avatar : self::default();
        $body = substr($avatar, strlen(self::PREFIX));

        if (str_contains($body, '__')) {
            [$style, $seed] = explode('__', $body, 2);
            $style = in_array($style, self::styles(), true) ? $style : 'adventurer';
            $seed = $seed !== '' ? $seed : 'red-picantes-01';

            return [$style, $seed];
        }

        return ['adventurer', $body ?: 'red-picantes-01'];
    }

    private static function label(string $style): string
    {
        return str($style)->replace('-', ' ')->headline()->toString();
    }

    private static function config(): array
    {
        static $config = null;

        if ($config !== null) {
            return $config;
        }

        $path = resource_path('data/avatar-library.json');
        $config = json_decode((string) file_get_contents($path), true);

        return $config;
    }
}
