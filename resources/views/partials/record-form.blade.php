@php
    $isEdit = $mode === 'edit';
    $formColumns = collect($columns)->filter(function ($col) use ($isEdit, $activeTable) {
        if (! $isEdit && ($col['auto_increment'] ?? false)) {
            return false;
        }
        return true;
    });
@endphp

<div class="{{ $classes['card'] }} {{ $isBootstrap ? 'card-body mb-3' : 'p-4 mb-4' }}">
    <h3 class="{{ $isBootstrap ? 'h6 mb-3' : 'text-sm font-semibold mb-3' }}">{{ $isEdit ? 'Edit record' : 'New record' }}</h3>

    <form method="post"
          action="{{ $isEdit
              ? route('db-manager.update', ['table' => $activeTable, 'pk' => rawurlencode((string) data_get($record, $primaryKey))])
              : route('db-manager.store', ['table' => $activeTable]) }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="{{ $isBootstrap ? 'row g-3' : 'grid gap-3 max-w-2xl' }}">
            @foreach ($formColumns as $column)
                @php
                    $name = $column['name'];
                    $value = old($name, $isEdit ? data_get($record, $name) : '');
                    if (is_array($value) || is_object($value)) {
                        $value = json_encode($value);
                    }
                    $type = strtolower($column['type_name'] ?? $column['type'] ?? 'string');
                    $inputType = 'text';
                    if (str_contains($type, 'int')) {
                        $inputType = 'number';
                    } elseif (str_contains($type, 'bool')) {
                        $inputType = 'checkbox';
                    }
                @endphp
                <div class="{{ $isBootstrap ? 'col-12 col-md-6' : '' }}">
                    <label class="{{ $isBootstrap ? 'form-label small' : 'block text-xs font-medium text-gray-700 mb-1' }}" for="field-{{ $name }}">
                        {{ $name }}
                        @if (! ($column['nullable'] ?? true))
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    @if ($inputType === 'checkbox')
                        <input type="hidden" name="{{ $name }}" value="0">
                        <input type="checkbox" name="{{ $name }}" id="field-{{ $name }}" value="1"
                               class="{{ $isBootstrap ? 'form-check-input' : '' }}"
                               {{ filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                    @elseif (str_contains($type, 'text') && ! str_contains($type, 'tiny'))
                        <textarea name="{{ $name }}" id="field-{{ $name }}" rows="3"
                                  class="{{ $classes['input'] }}">{{ $value }}</textarea>
                    @else
                        <input type="{{ $inputType }}" name="{{ $name }}" id="field-{{ $name }}"
                               value="{{ $inputType === 'checkbox' ? '' : $value }}"
                               class="{{ $classes['input'] }}">
                    @endif
                </div>
            @endforeach
        </div>

        <div class="{{ $isBootstrap ? 'mt-3 d-flex gap-2' : 'mt-4 flex gap-2' }}">
            <button type="submit" class="{{ $classes['btn'] }} {{ $classes['btnPrimary'] }}">
                {{ $isEdit ? 'Update' : 'Create' }}
            </button>
            <a href="{{ route('db-manager.index', ['table' => $activeTable]) }}"
               class="{{ $classes['btn'] }} {{ $classes['btnSecondary'] }}">Cancel</a>
        </div>
    </form>
</div>
