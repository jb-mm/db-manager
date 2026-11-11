<?php

namespace Jb\DbManager\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Jb\DbManager\Tests\TestCase;

class DbManagerTest extends TestCase
{
    public function test_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('db-manager.index'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->app['env'] = 'local';
    }

    public function test_index_works_in_local_environment(): void
    {
        $response = $this->get('/db-manager');

        $response->assertOk();
        $response->assertSee('Database Manager');
        $response->assertSee('items');
    }

    public function test_table_list_excludes_migrations(): void
    {
        $response = $this->get('/db-manager?table=items');

        $response->assertOk();
        $response->assertDontSee('migrations', false);
    }
}
