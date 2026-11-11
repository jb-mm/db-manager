<?php

namespace Jbmm\DbTableManager;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;
use Jbmm\DbTableManager\Services\UiFrameworkDetector;
use Jbmm\DbTableManager\Support\UiAssets;
use Jbmm\DbTableManager\Http\Middleware\EnsureDbTableManagerEnabled;
use Jbmm\DbTableManager\Services\RecordService;
use Jbmm\DbTableManager\Services\TableIntrospectionService;

class DbTableManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/db-manager.php', 'db-manager');

        $this->app->singleton(TableIntrospectionService::class);
        $this->app->singleton(RecordService::class);
        $this->app->singleton(UiFrameworkDetector::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'db-manager');

        View::composer('db-manager::*', function (ViewInstance $view) {
            $data = $view->getData();
            $theme = $data['ui'] ?? $this->app->make(UiFrameworkDetector::class)->detect();

            $view->with([
                'useProjectAssets' => UiAssets::useProjectAssets(),
                'viteEntries' => UiAssets::viteEntries(),
                'tailwindCdnUrl' => UiAssets::tailwindCdnUrl(),
                'bootstrapCdnUrl' => UiAssets::bootstrapCdnUrl($theme),
            ]);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/db-manager.php' => config_path('db-manager.php'),
            ], 'db-manager-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/db-manager'),
            ], 'db-manager-views');
        }

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('db-manager.enabled', EnsureDbTableManagerEnabled::class);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
