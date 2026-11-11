<?php

namespace Jbmm\DbTableManager\Tests\Unit;

use Illuminate\Http\Request;
use Jbmm\DbTableManager\Http\Middleware\EnsureDbTableManagerEnabled;
use Jbmm\DbTableManager\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EnsureDbTableManagerEnabledTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        // No migrations needed.
    }

    public function test_aborts_when_not_local_and_not_enabled(): void
    {
        $this->app['env'] = 'production';
        $this->app['config']->set('db-manager.enabled', false);

        $middleware = new EnsureDbTableManagerEnabled;

        $this->expectException(NotFoundHttpException::class);

        $middleware->handle(Request::create('/db-manager'), fn () => response('ok'));
    }

    public function test_allows_local_environment(): void
    {
        $this->app['env'] = 'local';

        $middleware = new EnsureDbTableManagerEnabled;
        $response = $middleware->handle(Request::create('/db-manager'), fn () => response('ok'));

        $this->assertSame(200, $response->getStatusCode());
    }
}
