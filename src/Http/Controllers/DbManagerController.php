<?php

namespace Jbmm\DbManager\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Jbmm\DbManager\Services\RecordService;
use Jbmm\DbManager\Services\TableIntrospectionService;
use Jbmm\DbManager\Services\UiFrameworkDetector;
use Jbmm\DbManager\Support\UiClassMap;
use Jbmm\DbManager\Support\UiTheme;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DbManagerController extends Controller
{
    public function __construct(
        protected TableIntrospectionService $introspection,
        protected RecordService $records,
        protected UiFrameworkDetector $uiDetector
    ) {}

    public function index(Request $request): View
    {
        $theme = $this->uiDetector->detect();
        $tables = $this->introspection->listTables();
        $activeTable = $request->query('table');
        $search = $request->query('search');
        $editId = $request->query('edit');
        $create = $request->boolean('create');

        $columns = [];
        $records = null;
        $primaryKey = null;
        $editRecord = null;
        $readOnly = false;
        $error = null;

        if ($activeTable !== null && $activeTable !== '') {
            if (! $this->introspection->tableExists($activeTable)) {
                throw new NotFoundHttpException('Table not found.');
            }

            try {
                $primaryKey = $this->introspection->getPrimaryKeyColumn($activeTable);
                $columns = $this->introspection->getColumns($activeTable);
                $readOnly = $this->introspection->isReadOnly($activeTable);
                $records = $this->records->paginate(
                    $activeTable,
                    is_string($search) ? $search : null,
                    max(1, (int) $request->query('page', 1))
                );

                if ($editId !== null && $editId !== '' && $primaryKey !== null) {
                    $editRecord = $this->records->findForEdit($activeTable, (string) $editId);
                }
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
            }
        }

        return view('db-manager::index', [
            'ui' => $theme,
            'layout' => UiTheme::layout($theme),
            'isBootstrap' => UiTheme::isBootstrap($theme),
            'classes' => UiClassMap::for(UiTheme::classMapKey($theme)),
            'tables' => $tables,
            'activeTable' => $activeTable,
            'columns' => $columns,
            'records' => $records,
            'primaryKey' => $primaryKey,
            'search' => $search ?? '',
            'readOnly' => $readOnly,
            'editRecord' => $editRecord,
            'showCreate' => $create && ! $readOnly,
            'error' => $error,
            'routePrefix' => config('db-manager.route_prefix', 'db-manager'),
        ]);
    }

    public function store(Request $request, string $table): RedirectResponse
    {
        $this->assertTable($table);

        $this->records->create($table, $request->except(['_token']));

        return redirect()->route('db-manager.index', [
            'table' => $table,
        ])->with('status', 'Record created.');
    }

    public function update(Request $request, string $table, string $pk): RedirectResponse
    {
        $this->assertTable($table);

        $this->records->update($table, $pk, $request->except(['_token', '_method']));

        return redirect()->route('db-manager.index', [
            'table' => $table,
        ])->with('status', 'Record updated.');
    }

    public function destroy(Request $request, string $table, string $pk): RedirectResponse
    {
        $this->assertTable($table);

        $this->records->delete($table, $pk);

        return redirect()->route('db-manager.index', [
            'table' => $table,
        ])->with('status', 'Record deleted.');
    }

    protected function assertTable(string $table): void
    {
        if (! $this->introspection->tableExists($table)) {
            throw new NotFoundHttpException('Table not found.');
        }
    }
}
