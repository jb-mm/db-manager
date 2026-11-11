<?php

namespace Jbmm\TableRecordManager\Tests\Unit;

use Jbmm\TableRecordManager\Support\UiAssets;
use Jbmm\TableRecordManager\Tests\TestCase;

class UiAssetsTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        // No migrations.
    }

    public function test_project_mode_ignores_cdn_urls(): void
    {
        $this->app['config']->set('db-manager.assets', 'project');
        $this->app['config']->set('db-manager.tailwind_cdn_url', 'https://example.test/tailwind.js');
        $this->app['config']->set('db-manager.bootstrap_cdn_urls', [
            'bootstrap5' => 'https://example.test/bootstrap5.css',
        ]);

        $this->assertNull(UiAssets::tailwindCdnUrl());
        $this->assertNull(UiAssets::bootstrapCdnUrl('bootstrap5'));
        $this->assertTrue(UiAssets::useProjectAssets());
    }

    public function test_cdn_mode_returns_configured_urls(): void
    {
        $this->app['config']->set('db-manager.assets', 'cdn');
        $this->app['config']->set('db-manager.tailwind_cdn_url', 'https://example.test/tailwind.js');
        $this->app['config']->set('db-manager.bootstrap_cdn_urls', [
            'bootstrap5' => 'https://example.test/bootstrap5.css',
            'bootstrap6' => 'https://example.test/bootstrap6.css',
        ]);

        $this->assertSame('https://example.test/tailwind.js', UiAssets::tailwindCdnUrl());
        $this->assertSame('https://example.test/bootstrap5.css', UiAssets::bootstrapCdnUrl('bootstrap5'));
        $this->assertSame('https://example.test/bootstrap6.css', UiAssets::bootstrapCdnUrl('bootstrap6'));
    }

    public function test_vite_entries_only_includes_existing_files(): void
    {
        $base = sys_get_temp_dir().'/db-manager-assets-'.uniqid();
        mkdir($base.'/resources/css', 0755, true);
        file_put_contents($base.'/resources/css/app.css', '/* app */');

        $this->app->setBasePath($base);
        $this->app['config']->set('db-manager.vite_entries', [
            'resources/css/app.css',
            'resources/css/missing.css',
        ]);

        $this->assertSame(['resources/css/app.css'], UiAssets::viteEntries());
    }
}
