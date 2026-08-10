@php
    $selected = $selected ?? '';
@endphp
<div class="searchable-select" data-searchable-select>
    <label class="visually-hidden" for="{{ $id }}">{{ $label }}</label>
    <button type="button" class="ss-trigger" id="{{ $id }}" data-ss-trigger
        aria-haspopup="listbox" aria-expanded="false" aria-label="{{ $label }}">
        <span class="ss-trigger-label">{{ $selected === '' || $selected === null ? $placeholder : $items->firstWhere('id', (int) $selected)?->name }}</span>
        <i class="bi bi-chevron-down ss-caret"></i>
    </button>
    <div class="ss-popover" data-ss-popover>
        <div class="ss-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control ss-search-input" data-ss-query
                placeholder="Cari {{ $label }}…" autocomplete="off">
        </div>
        <div class="ss-options" role="listbox" aria-label="{{ $label }}">
            <button type="button" class="ss-option @if ($selected === '' || $selected === null) is-selected @endif"
                role="option" data-ss-value="" data-ss-label="{{ $placeholder }}">
                {{ $placeholder }}
            </button>
            @foreach ($items as $item)
                <button type="button"
                    class="ss-option @if ((string) $selected === (string) $item->id) is-selected @endif"
                    role="option" data-ss-value="{{ $item->id }}" data-ss-label="{{ $item->name }}">
                    {{ $item->name }}
                </button>
            @endforeach
        </div>
        <div class="ss-empty d-none" data-ss-empty>Tidak ada hasil</div>
    </div>
    <select id="{{ $id }}-select" class="d-none" data-param="{{ $param }}" tabindex="-1" aria-hidden="true"
        @if (!empty($name)) name="{{ $name }}" @endif>
        <option value="">{{ $placeholder }}</option>
        @foreach ($items as $item)
            <option value="{{ $item->id }}" @selected((string) $selected === (string) $item->id)>{{ $item->name }}</option>
        @endforeach
    </select>
</div>
