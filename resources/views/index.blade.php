@extends($layout)

@section('content')
@php
    $sidebarWidth = $isBootstrap ? 'style="width: 16rem;"' : '';
@endphp
<div class="{{ $classes['shell'] }}">
    <aside class="{{ $classes['sidebar'] }}" {!! $sidebarWidth !!}>
        <div class="{{ $classes['sidebarTitle'] }}">Tables</div>
        <nav class="{{ $isBootstrap ? 'py-2' : '' }}">
            @forelse ($tables as $name)
                @php
                    $url = route('db-manager.index', ['table' => $name]);
                    $linkClass = ($activeTable === $name) ? $classes['sidebarLinkActive'] : $classes['sidebarLink'];
                @endphp
                <a href="{{ $url }}" class="{{ $linkClass }}">{{ $name }}</a>
            @empty
                <p class="px-4 py-2 text-sm text-gray-500">No tables found.</p>
            @endforelse
        </nav>
    </aside>

    <main class="{{ $classes['main'] }}">
        <h1 class="{{ $isBootstrap ? 'h4 mb-3' : 'text-xl font-semibold mb-4' }}">Database Manager</h1>

        @if (session('status'))
            <div class="{{ $classes['alert'] }}">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="{{ $classes['alertError'] }}">
                <ul class="mb-0">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($activeTable === null || $activeTable === '')
            <div class="{{ $classes['card'] }} {{ $isBootstrap ? 'card-body' : 'p-8 text-center text-gray-500' }}">
                Select a table from the left to view and manage records.
            </div>
        @elseif ($error)
            <div class="{{ $classes['alertError'] }}">{{ $error }}</div>
        @else
            <div class="{{ $isBootstrap ? 'd-flex flex-wrap gap-2 justify-content-between align-items-center mb-3' : 'flex flex-wrap gap-2 justify-between items-center mb-4' }}">
                <h2 class="{{ $isBootstrap ? 'h5 mb-0' : 'text-lg font-medium' }}">{{ $activeTable }}</h2>
                @if (! $readOnly)
                    <a href="{{ route('db-manager.index', ['table' => $activeTable, 'create' => 1]) }}"
                       class="{{ $classes['btn'] }} {{ $classes['btnPrimary'] }}">Add record</a>
                @else
                    <span class="{{ $isBootstrap ? 'badge text-bg-secondary' : 'text-xs text-gray-500' }}">Read-only</span>
                @endif
            </div>

            <form method="get" action="{{ route('db-manager.index') }}" class="{{ $isBootstrap ? 'row g-2 mb-3' : 'flex gap-2 mb-4' }}">
                <input type="hidden" name="table" value="{{ $activeTable }}">
                <input type="search" name="search" value="{{ $search }}" placeholder="Search…"
                       class="{{ $classes['input'] }} {{ ! $isBootstrap ? 'max-w-md' : 'col' }}">
                <button type="submit" class="{{ $classes['btn'] }} {{ $classes['btnSecondary'] }}">Search</button>
                @if ($search !== '')
                    <a href="{{ route('db-manager.index', ['table' => $activeTable]) }}"
                       class="{{ $classes['btn'] }} {{ $classes['btnSecondary'] }}">Clear</a>
                @endif
            </form>

            @if ($showCreate || $editRecord)
                @include('db-manager::partials.record-form', [
                    'mode' => $editRecord ? 'edit' : 'create',
                    'record' => $editRecord,
                ])
            @endif

            @if ($records && $primaryKey)
                <div class="{{ $classes['card'] }} {{ $isBootstrap ? '' : 'overflow-x-auto' }}">
                    <div class="{{ $isBootstrap ? 'table-responsive' : '' }}">
                        <table class="{{ $classes['table'] }}">
                            <thead>
                                <tr>
                                    @foreach ($columns as $column)
                                        <th class="{{ $classes['th'] }}">{{ $column['name'] }}</th>
                                    @endforeach
                                    @if (! $readOnly)
                                        <th class="{{ $classes['th'] }}">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($records as $row)
                                    <tr>
                                        @foreach ($columns as $column)
                                            @php
                                                $val = data_get($row, $column['name']);
                                                if (is_array($val) || is_object($val)) {
                                                    $val = json_encode($val);
                                                }
                                                $display = $val === null ? '—' : \Illuminate\Support\Str::limit((string) $val, 80);
                                            @endphp
                                            <td class="{{ $classes['td'] }}" title="{{ $val !== null ? (string) $val : '' }}">{{ $display }}</td>
                                        @endforeach
                                        @if (! $readOnly)
                                            <td class="{{ $classes['td'] }}">
                                                <div class="{{ $isBootstrap ? 'd-flex gap-1' : 'flex gap-2' }}">
                                                    <a href="{{ route('db-manager.index', ['table' => $activeTable, 'edit' => data_get($row, $primaryKey)]) }}"
                                                       class="{{ $classes['btn'] }} {{ $classes['btnSecondary'] }}">Edit</a>
                                                    <form method="post" action="{{ route('db-manager.destroy', ['table' => $activeTable, 'pk' => rawurlencode((string) data_get($row, $primaryKey))]) }}"
                                                          onsubmit="return confirm('Delete this record?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="{{ $classes['btn'] }} {{ $classes['btnDanger'] }}">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($columns) + ($readOnly ? 0 : 1) }}" class="{{ $classes['td'] }} text-center text-muted">No records.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @php
                    $records->appends(['table' => $activeTable, 'search' => $search]);
                @endphp
                <div class="{{ $isBootstrap ? 'mt-3' : 'mt-4' }}">
                    @include('db-manager::partials.pagination', ['paginator' => $records, 'classes' => $classes, 'isBootstrap' => $isBootstrap])
                </div>
            @elseif (! $primaryKey)
                <div class="{{ $classes['alertError'] }}">This table has no single-column primary key.</div>
            @endif
        @endif
    </main>
</div>
@endsection
