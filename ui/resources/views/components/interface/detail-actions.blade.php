{{--
    Action bar at the bottom of a detail panel (row detail, record page).

    Left ("start" slot): destructive actions — delete (outline danger),
    cancel a document (outline warning).
    Right (default slot): secondary actions (outline secondary: copy, print,
    export, return...) then the main action last (primary: edit, save).
    All buttons size="sm" with an icon and a label.

    <x-ui::detail-actions>
        <x-slot:start>
            <x-ui::button color="danger" outline size="sm" icon="trash" wire:click="..." wire:confirm="...">{{ __('core/base::general.delete') }}</x-ui::button>
        </x-slot:start>
        <x-ui::button color="secondary" outline size="sm" icon="copy" ...>{{ __('core/base::general.copy') }}</x-ui::button>
        <x-ui::button color="primary" size="sm" icon="pencil" ...>{{ __('core/base::general.edit') }}</x-ui::button>
    </x-ui::detail-actions>
--}}
<div {{ $attributes->merge(['class' => 'detail-actions']) }}>
    <div class="detail-actions-start">
        {{ $start ?? '' }}
    </div>
    <div class="detail-actions-end">
        {{ $slot }}
    </div>
</div>
