{{-- Rename Modal --}}
@if($showRenameModal)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        {!! tabler_icon('edit', ['class' => 'icon text-primary']) !!}
                        {{ __('core/media::media.rename') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showRenameModal', false)"></button>
                </div>
                <div class="modal-body">
                    <x-ui.form.input
                        wire:model="renameItemName"
                        :label="__('core/media::media.new_name')"
                        :placeholder="__('core/media::media.enter_new_name')"
                        icon="edit"
                        wire:keydown.enter="renameItem"
                        required
                        autofocus
                    />
                </div>
                <div class="modal-footer">
                    <x-ui::button color="secondary" wire:click="$set('showRenameModal', false')">
                        {{ __('core/media::media.cancel') }}
                    </x-ui::button>
                    <x-ui::button color="primary" wire:click="renameItem" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="renameItem">
                            {{ __('core/media::media.save') }}
                        </span>
                        <span wire:loading wire:target="renameItem">
                            {{ __('core/media::media.saving') }}
                        </span>
                    </x-ui::button>
                </div>
            </div>
        </div>
    </div>
@endif
