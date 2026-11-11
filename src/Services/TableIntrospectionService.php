<?php

namespace Jbmm\TableRecordManager\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class TableIntrospectionService
{
    public function connectionName(): string
    {
        return config('db-manager.connection') ?? config('database.default');
    }

    public function connection()
    {
        return DB::connection($this->connectionName());
    }

    public function schema()
    {
        return Schema::connection($this->connectionName());
    }

    /**
     * @return list<string>
     */
    public function listTables(): array
    {
        $tables = $this->schema()->getTableListing(schema: null, schemaQualified: false);
        $excluded = config('db-manager.excluded_tables', []);

        $filtered = array_values(array_filter(
            $tables,
            fn (string $name) => ! in_array($name, $excluded, true)
        ));

        sort($filtered);

        return $filtered;
    }

    public function tableExists(string $table): bool
    {
        return in_array($table, $this->listTables(), true);
    }

    public function isReadOnly(string $table): bool
    {
        return in_array($table, config('db-manager.read_only_tables', []), true);
    }

    /**
     * @return list<string>
     */
    public function hiddenColumns(): array
    {
        return config('db-manager.hidden_columns', []);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getColumns(string $table): array
    {
        $hidden = $this->hiddenColumns();

        return array_values(array_filter(
            $this->schema()->getColumns($table),
            fn (array $column) => ! in_array($column['name'], $hidden, true)
        ));
    }

    /**
     * @return list<string>
     */
    public function getPrimaryKeyColumns(string $table): array
    {
        $indexes = $this->schema()->getIndexes($table);

        foreach ($indexes as $index) {
            if (($index['primary'] ?? false) === true) {
                return $index['columns'];
            }
        }

        return [];
    }

    public function getPrimaryKeyColumn(string $table): ?string
    {
        $columns = $this->getPrimaryKeyColumns($table);

        if (count($columns) === 0) {
            return null;
        }

        if (count($columns) > 1) {
            throw new RuntimeException("Table [{$table}] has a composite primary key, which is not supported.");
        }

        return $columns[0];
    }

    public function isAutoIncrement(string $table, string $column): bool
    {
        foreach ($this->getColumns($table) as $col) {
            if ($col['name'] === $column) {
                return ($col['auto_increment'] ?? false) === true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public function searchableColumns(string $table): array
    {
        $searchableTypes = [
            'string', 'text', 'varchar', 'char', 'json', 'mediumtext', 'longtext',
        ];

        return array_values(array_map(
            fn (array $col) => $col['name'],
            array_filter($this->getColumns($table), function (array $col) use ($searchableTypes) {
                $type = strtolower($col['type_name'] ?? $col['type'] ?? '');

                foreach ($searchableTypes as $needle) {
                    if (str_contains($type, $needle)) {
                        return true;
                    }
                }

                return false;
            })
        ));
    }

    /**
     * @return list<string>
     */
    public function editableColumnNames(string $table, bool $forCreate): array
    {
        $pk = $this->getPrimaryKeyColumn($table);

        return array_values(array_map(
            fn (array $col) => $col['name'],
            array_filter($this->getColumns($table), function (array $col) use ($table, $pk, $forCreate) {
                if ($forCreate && $pk === $col['name'] && $this->isAutoIncrement($table, $pk)) {
                    return false;
                }

                return true;
            })
        ));
    }
}
