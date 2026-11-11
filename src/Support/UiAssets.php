<?php

namespace Jb\DbManager\Support;

class UiAssets
{
    public static function mode(): string
    {
        $mode = strtolower((string) config('db-manager.assets', 'project'));

        return in_array($mode, ['project', 'cdn'], true) ? $mode : 'project';
    }

    public static function useProjectAssets(): bool
    {
        return self::mode() === 'project';
    }

    /**
     * @return list<string>
     */
    public static function viteEntries(): array
    {
        $entries = config('db-manager.vite_entries', ['resources/css/app.css']);

        if (! is_array($entries)) {
            return [];
        }

        return array_values(array_filter(
            $entries,
            fn (string $path) => file_exists(base_path($path))
        ));
    }

    public static function tailwindCdnUrl(): ?string
    {
        if (self::mode() !== 'cdn') {
            return null;
        }

        $url = config('db-manager.tailwind_cdn_url');

        return is_string($url) && $url !== '' ? $url : null;
    }

    public static function bootstrapCdnUrl(string $theme): ?string
    {
        if (self::mode() !== 'cdn') {
            return null;
        }

        $theme = UiTheme::normalize($theme);

        if (! in_array($theme, ['bootstrap5', 'bootstrap6'], true)) {
            return null;
        }

        $urls = config('db-manager.bootstrap_cdn_urls', []);
        $url = $urls[$theme] ?? null;

        return is_string($url) && $url !== '' ? $url : null;
    }
}
