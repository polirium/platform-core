<div>
    @if ($open_inline)
        <form wire:submit.prevent="save">
            <x-form::select wire:model="value" tomselect :options="$options">
                <x-slot name="append">
                    <x-ui.button type="submit" color="success" icon="device-floppy" />
                    <x-ui.button type="button" color="danger" icon="x" wire:click="toggleInline" />
                </x-slot>
            </x-form::select>
        </form>
    @else
        <span wire:click="toggleInline" class="cursor-pointer text-primary">
            <b>{!! ! empty($text_display) ? $text_display : 'null' !!}</b>
        </span>
    @endif
</div>
