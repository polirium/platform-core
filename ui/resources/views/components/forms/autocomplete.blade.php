@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'hint' => null,
])

@php
    $wireModel = $attributes->wire('model')->value() ?? 'search';
@endphp

@if ($label)
    <x-form::label :description="$description">{{ $label }}</x-form::label>
@endif

<span class="position-relative"
       x-data="{ show: false }">
    <div class="input-icon">
        <input {{ $attributes->class(["form-control"]) }}
            @focus="show = true"
            @input="show = true"
            @click="show = true"
            @blur="setTimeout(() => show = false, 200)"
        />
        <span class="input-icon-addon">
            <span wire:loading.delay
                  wire:target="{{ $wireModel }}"
                  class="d-none spinner-border spinner-border-sm text-primary"
                  role="status"
                  aria-hidden="true"
                  style="width: 1rem; height: 1rem; border-width: 2px;">
            </span>
        </span>
    </div>
    <div class="list-group list-group-flush bg-light position-absolute w-100 shadow custom-scrollbar"
         style="z-index: 1050; max-height: 300px; overflow-y: auto; margin-top: 4px;"
         x-show="show"
         x-cloak>
        {{-- Skeleton loading state --}}
        <div wire:loading wire:target="{{ $wireModel }}" class="list-group-item p-2">
            @for ($i = 0; $i < 3; $i++)
                <div class="d-flex align-items-center gap-2 py-1">
                    <div style="width: 16px; height: 16px; border-radius: 4px; background: #e9ecef; animation: skeleton-pulse 1.5s ease-in-out infinite;"></div>
                    <div class="flex-grow-1">
                        <div style="width: {{ 50 + ($i * 10) }}%; height: 10px; border-radius: 4px; background: #e9ecef; animation: skeleton-pulse 1.5s ease-in-out infinite; animation-delay: {{ $i * 0.1 }}s;"></div>
                    </div>
                </div>
            @endfor
        </div>
        {{ $slot }}
    </div>
</span>

@if ($hint)
    <small class="form-hint">{{ $hint }}</small>
@endif

<style>
    @keyframes skeleton-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
