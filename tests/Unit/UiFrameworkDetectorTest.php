<?php

namespace Jb\DbManager\Tests\Unit;

use Jb\DbManager\Services\UiFrameworkDetector;
use Jb\DbManager\Tests\TestCase;

class UiFrameworkDetectorTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        // No migrations.
    }

    public function test_explicit_tailwind(): void
    {
        $this->app['config']->set('db-manager.ui', 'tailwind');

        $this->assertSame('tailwind', app(UiFrameworkDetector::class)->detect());
    }

    public function test_explicit_bootstrap5_and_bootstrap6(): void
    {
        $detector = app(UiFrameworkDetector::class);

        $this->app['config']->set('db-manager.ui', 'bootstrap5');
        $this->assertSame('bootstrap5', $detector->detect());

        $this->app['config']->set('db-manager.ui', 'bootstrap6');
        $this->assertSame('bootstrap6', $detector->detect());
    }

    public function test_bootstrap_alias_maps_to_bootstrap5(): void
    {
        $this->app['config']->set('db-manager.ui', 'bootstrap');

        $this->assertSame('bootstrap5', app(UiFrameworkDetector::class)->detect());
    }

    public function test_auto_detects_bootstrap5_from_package_json(): void
    {
        $base = sys_get_temp_dir().'/db-manager-ui-'.uniqid();
        mkdir($base);
        file_put_contents($base.'/package.json', json_encode([
            'devDependencies' => ['bootstrap' => '^5.3.3'],
        ]));

        $this->app->setBasePath($base);
        $this->app['config']->set('db-manager.ui', 'auto');

        $this->assertSame('bootstrap5', app(UiFrameworkDetector::class)->detect());
    }

    public function test_auto_detects_bootstrap6_from_package_json(): void
    {
        $base = sys_get_temp_dir().'/db-manager-ui-'.uniqid();
        mkdir($base);
        file_put_contents($base.'/package.json', json_encode([
            'devDependencies' => ['bootstrap' => '^6.0.0'],
        ]));

        $this->app->setBasePath($base);
        $this->app['config']->set('db-manager.ui', 'auto');

        $this->assertSame('bootstrap6', app(UiFrameworkDetector::class)->detect());
    }

    public function test_auto_prefers_tailwind_over_bootstrap(): void
    {
        $base = sys_get_temp_dir().'/db-manager-ui-'.uniqid();
        mkdir($base);
        file_put_contents($base.'/package.json', json_encode([
            'devDependencies' => [
                'tailwindcss' => '^3.4.0',
                'bootstrap' => '^6.0.0',
            ],
        ]));
        file_put_contents($base.'/tailwind.config.js', 'module.exports = {};');

        $this->app->setBasePath($base);
        $this->app['config']->set('db-manager.ui', 'auto');

        $this->assertSame('tailwind', app(UiFrameworkDetector::class)->detect());
    }
}
