@php
    $tabs = [
        'all' => ['label' => __('core/base::general.all'), 'icon' => 'list'],
        'created' => ['label' => __('core/base::general.created'), 'icon' => 'plus'],
        'updated' => ['label' => __('core/base::general.updated'), 'icon' => 'pencil'],
        'deleted' => ['label' => __('core/base::general.deleted'), 'icon' => 'trash']
    ];
@endphp

<div class="mb-3">
    <div class="btn-group">
        @foreach ($tabs as $key => $tab)
            <button type="button"
                    class="btn {{ $currentEvent === $key ? 'btn-primary' : 'btn-outline-secondary' }}"
                    wire:click="setEvent('{{ $key }}')">
                {!! tabler_icon($tab['icon']) !!} {{ $tab['label'] }}
            </button>
        @endforeach
    </div>
</div>
