{{-- Rename Modal --}}
@if($showRenameModal)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-3">
                    <h5 class="modal-title">
                        <i class="ti ti-edit me-2"></i>
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
                <div class="modal-footer py-3">
                    <button type="button" wire:click="$set('showRenameModal', false)" class="btn btn-ghost-secondary">
                        {{ __('core/media::media.cancel') }}
                    </button>
                    <button type="button" wire:click="renameItem" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="renameItem">
                            <i class="ti ti-device-floppy me-1"></i>
                            {{ __('core/media::media.save') }}
                        </span>
                        <span wire:loading wire:target="renameItem">
                            <i class="ti ti-loader-2 icon-spin me-1"></i>
                            {{ __('core/media::media.saving') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
