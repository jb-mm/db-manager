<?php

namespace Jbmm\DbManager\Support;

class UiTheme
{
    public static function normalize(string $ui): string
    {
        $ui = strtolower(trim($ui));

        if ($ui === 'bootstrap') {
            return 'bootstrap5';
        }

        return $ui;
    }

    public static function isBootstrap(string $theme): bool
    {
        return str_starts_with(self::normalize($theme), 'bootstrap');
    }

    public static function layout(string $theme): string
    {
        return self::isBootstrap($theme)
            ? 'db-manager::layouts.bootstrap'
            : 'db-manager::layouts.tailwind';
    }

    public static function classMapKey(string $theme): string
    {
        return self::isBootstrap($theme) ? 'bootstrap' : 'tailwind';
    }

    public static function bootstrapCdnUrl(string $theme): ?string
    {
        return UiAssets::bootstrapCdnUrl($theme);
    }
}
