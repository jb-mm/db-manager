<?php

namespace Jbmm\DbManager\Services;

use Jbmm\DbManager\Support\UiTheme;

class UiFrameworkDetector
{
    public const THEME_TAILWIND = 'tailwind';

    public const THEME_BOOTSTRAP5 = 'bootstrap5';

    public const THEME_BOOTSTRAP6 = 'bootstrap6';

    public function detect(): string
    {
        $configured = UiTheme::normalize((string) config('db-manager.ui', 'auto'));

        if ($configured !== 'auto') {
            return $this->resolveExplicit($configured);
        }

        if ($this->hostUsesTailwind()) {
            return self::THEME_TAILWIND;
        }

        $bootstrap = $this->detectHostBootstrapVersion();
        if ($bootstrap !== null) {
            return $bootstrap;
        }

        return self::THEME_TAILWIND;
    }

    protected function resolveExplicit(string $configured): string
    {
        $allowed = [
            self::THEME_TAILWIND,
            self::THEME_BOOTSTRAP5,
            self::THEME_BOOTSTRAP6,
        ];

        if (in_array($configured, $allowed, true)) {
            return $configured;
        }

        return self::THEME_TAILWIND;
    }

    protected function hostUsesTailwind(): bool
    {
        if (file_exists(base_path('tailwind.config.js'))) {
            return true;
        }

        $vite = base_path('vite.config.js');
        if (file_exists($vite) && str_contains((string) file_get_contents($vite), 'tailwindcss')) {
            return true;
        }

        $packageJson = base_path('package.json');
        if (file_exists($packageJson)) {
            $data = json_decode((string) file_get_contents($packageJson), true);
            $deps = array_merge($data['dependencies'] ?? [], $data['devDependencies'] ?? []);

            if (isset($deps['tailwindcss'])) {
                return true;
            }
        }

        return false;
    }

    protected function detectHostBootstrapVersion(): ?string
    {
        $packageJson = base_path('package.json');
        if (file_exists($packageJson)) {
            $data = json_decode((string) file_get_contents($packageJson), true);
            $deps = array_merge($data['dependencies'] ?? [], $data['devDependencies'] ?? []);

            if (isset($deps['bootstrap'])) {
                return $this->bootstrapThemeFromConstraint((string) $deps['bootstrap']);
            }
        }

        foreach ([
            resource_path('scss/app.scss'),
            resource_path('sass/app.scss'),
            resource_path('css/app.css'),
        ] as $path) {
            if (file_exists($path) && stripos((string) file_get_contents($path), 'bootstrap') !== false) {
                return self::THEME_BOOTSTRAP5;
            }
        }

        return null;
    }

    protected function bootstrapThemeFromConstraint(string $constraint): string
    {
        $constraint = trim($constraint);

        if ($constraint === '' || $constraint === '*') {
            return self::THEME_BOOTSTRAP5;
        }

        if (preg_match('/^\^?\s*6(?:\.|$|[-\s])/', $constraint)) {
            return self::THEME_BOOTSTRAP6;
        }

        if (preg_match('/^>=\s*6/', $constraint)) {
            return self::THEME_BOOTSTRAP6;
        }

        if (preg_match('/^6\./', $constraint)) {
            return self::THEME_BOOTSTRAP6;
        }

        return self::THEME_BOOTSTRAP5;
    }
}
