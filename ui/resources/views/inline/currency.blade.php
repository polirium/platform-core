<div>
    @if ($open_inline)
        <form wire:submit.prevent="save">
            <x-form::input type="number" wire:model="value">
                <x-slot name="append">
                    <x-ui.button type="submit" color="success" icon="device-floppy" />
                    <x-ui.button type="button" color="danger" icon="x" wire:click="toggleInline" />
                </x-slot>
            </x-form::input>
        </form>
    @else
        <span wire:click="toggleInline" class="cursor-pointer text-primary">
            <b>{!! core_number_format($text_display) !!}</b>
        </span>
    @endif
</div>
