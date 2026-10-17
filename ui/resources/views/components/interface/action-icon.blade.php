{{--
    One row action: an icon-only button with a tooltip (the standard for row
    actions in tables and lists). Use inside <x-ui::row-actions>.

    <x-ui::action-icon icon="pencil" :label="__('core/base::general.edit')" wire:click="edit({{ $id }})" />
    <x-ui::action-icon icon="trash" danger :label="__('core/base::general.delete')"
        :confirm="__('core/base::general.delete_confirm')" wire:click="delete({{ $id }})" />
    <x-ui::action-icon icon="printer" :label="__('Print')" href="{{ $url }}" target="_blank" />

    Order in a group: view, edit, copy, print, other, delete (delete last).
--}}
@props([
    'icon',
    'label',
    'danger' => false,
    'href' => null,
    'confirm' => null,
])

@php
    $classes = 'btn btn-sm btn-icon ' . ($danger ? 'btn-ghost-danger' : 'btn-ghost-secondary');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'title' => $label, 'aria-label' => $label]) }}>
        {!! tabler_icon($icon, ['class' => 'icon']) !!}
    </a>
@else
    <button type="button" @if ($confirm) wire:confirm="{{ $confirm }}" @endif
        {{ $attributes->merge(['class' => $classes, 'title' => $label, 'aria-label' => $label]) }}>
        {!! tabler_icon($icon, ['class' => 'icon']) !!}
    </button>
@endif
