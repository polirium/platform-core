@php
    $tabs = [
        'all' => ['label' => 'Tất cả', 'icon' => 'list'],
        'created' => ['label' => 'Thêm mới', 'icon' => 'plus'],
        'updated' => ['label' => 'Cập nhật', 'icon' => 'edit'],
        'deleted' => ['label' => 'Xóa', 'icon' => 'trash'],
    ];
@endphp

<div class="mb-3">
    <div class="btn-group">
        @foreach ($tabs as $key => $tab)
            <button type="button"
                    class="btn {{ $currentEvent === $key ? 'btn-primary' : 'btn-outline-secondary' }}"
                    wire:click="setEvent('{{ $key }}')">
                <i class="icon ti ti-{{ $tab['icon'] }}"></i> {{ $tab['label'] }}
            </button>
        @endforeach
    </div>
</div>
