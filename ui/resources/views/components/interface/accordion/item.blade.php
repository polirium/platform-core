@props([
    'title'     => null,
    'parent'    => null,
    'id'        => null,
])

<div class="accordion-item" {{ $attributes }} wire:ignore.self>
    <h2 class="accordion-header">
        <button type="button" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $id }}-{{ $parent }}" aria-expanded="false">
            {{ $title }}
        </button>
    </h2>
    <div id="{{ $id }}-{{ $parent }}" class="accordion-collapse collapse" data-bs-parent="#{{ $parent }}" wire:ignore.self>
        <div class="accordion-body pt-0">
            {{ $slot }}
        </div>
    </div>
</div>
