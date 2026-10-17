{{--
    Group of row actions (icon buttons) in a table cell or list item.

    <x-ui::row-actions>
        <x-ui::action-icon icon="eye" ... />
        <x-ui::action-icon icon="pencil" ... />
        <x-ui::action-icon icon="trash" danger ... />
    </x-ui::row-actions>
--}}
<div {{ $attributes->merge(['class' => 'row-actions']) }}>
    {{ $slot }}
</div>
