<?php

namespace Jbmm\TableRecordManager\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RecordService
{
    public function __construct(
        protected TableIntrospectionService $introspection
    ) {}

    public function paginate(string $table, ?string $search = null, int $page = 1): LengthAwarePaginator
    {
        $perPage = (int) config('db-manager.per_page', 25);
        $query = $this->introspection->connection()->table($table);

        if ($search !== null && $search !== '') {
            $columns = $this->introspection->searchableColumns($table);
            if ($columns !== []) {
                $query->where(function ($q) use ($columns, $search) {
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            }
        }

        $pk = $this->introspection->getPrimaryKeyColumn($table);
        if ($pk !== null) {
            $query->orderBy($pk);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function create(string $table, array $input): void
    {
        $this->assertWritable($table);
        $data = $this->filterAndCast($table, $input, true);
        $this->validate($table, $data, true);
        $this->introspection->connection()->table($table)->insert($data);
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function update(string $table, string $pkValue, array $input): int
    {
        $this->assertWritable($table);
        $pk = $this->introspection->getPrimaryKeyColumn($table);
        $data = $this->filterAndCast($table, $input, false);
        unset($data[$pk]);
        $this->validate($table, $data, false);

        return $this->introspection->connection()->table($table)
            ->where($pk, $this->castPk($table, $pk, $pkValue))
            ->update($data);
    }

    public function delete(string $table, string $pkValue): int
    {
        $this->assertWritable($table);
        $pk = $this->introspection->getPrimaryKeyColumn($table);

        return $this->introspection->connection()->table($table)
            ->where($pk, $this->castPk($table, $pk, $pkValue))
            ->delete();
    }

    public function findForEdit(string $table, string $pkValue): ?object
    {
        $pk = $this->introspection->getPrimaryKeyColumn($table);

        return $this->introspection->connection()->table($table)
            ->where($pk, $this->castPk($table, $pk, $pkValue))
            ->first();
    }

    protected function assertWritable(string $table): void
    {
        if ($this->introspection->isReadOnly($table)) {
            throw ValidationException::withMessages([
                'table' => ["Table [{$table}] is read-only."],
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    protected function filterAndCast(string $table, array $input, bool $forCreate): array
    {
        $allowed = $this->introspection->editableColumnNames($table, $forCreate);
        $data = [];

        foreach ($allowed as $column) {
            if (! array_key_exists($column, $input)) {
                continue;
            }
            $value = $input[$column];
            if ($value === '' || $value === null) {
                $data[$column] = null;

                continue;
            }
            $data[$column] = $this->castValue($table, $column, $value);
        }

        return $data;
    }

    protected function castValue(string $table, string $column, mixed $value): mixed
    {
        $type = $this->columnType($table, $column);
        $type = strtolower($type);

        if (str_contains($type, 'int') || str_contains($type, 'bigint')) {
            return (int) $value;
        }
        if (str_contains($type, 'bool') || str_contains($type, 'tinyint(1)')) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        if (str_contains($type, 'json')) {
            if (is_string($value)) {
                json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw ValidationException::withMessages([
                        $column => ['Invalid JSON.'],
                    ]);
                }
            }

            return is_string($value) ? $value : json_encode($value);
        }
        if (str_contains($type, 'float') || str_contains($type, 'double') || str_contains($type, 'decimal')) {
            return (float) $value;
        }

        return $value;
    }

    protected function castPk(string $table, string $pk, string $pkValue): mixed
    {
        return $this->castValue($table, $pk, $pkValue);
    }

    protected function columnType(string $table, string $column): string
    {
        foreach ($this->introspection->getColumns($table) as $col) {
            if ($col['name'] === $column) {
                return (string) ($col['type_name'] ?? $col['type'] ?? 'string');
            }
        }

        return 'string';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function validate(string $table, array $data, bool $forCreate): void
    {
        $rules = [];
        foreach ($this->introspection->getColumns($table) as $col) {
            $name = $col['name'];
            if (! array_key_exists($name, $data) && ! $forCreate) {
                continue;
            }
            if (! in_array($name, $this->introspection->editableColumnNames($table, $forCreate), true)) {
                continue;
            }

            $nullable = ($col['nullable'] ?? true) === true;
            $rule = [$nullable ? 'nullable' : 'required'];

            $type = strtolower($col['type_name'] ?? $col['type'] ?? '');
            if (str_contains($type, 'int')) {
                $rule[] = 'integer';
            } elseif (str_contains($type, 'bool')) {
                $rule[] = 'boolean';
            } elseif (str_contains($type, 'json')) {
                $rule[] = 'string';
            } else {
                $rule[] = 'string';
            }

            $rules[$name] = $rule;
        }

        Validator::make($data, $rules)->validate();
    }
}
