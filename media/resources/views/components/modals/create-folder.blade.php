{{-- Create Folder Modal --}}
@if($showCreateFolderModal)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-3">
                    <h5 class="modal-title">
                        <i class="ti ti-folder-plus me-2"></i>
                        {{ __('core/media::media.create_folder_new') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showCreateFolderModal', false)"></button>
                </div>
                <div class="modal-body">
                    <x-ui.form.input
                        wire:model="newFolderName"
                        :label="__('core/media::media.folder_name')"
                        :placeholder="__('core/media::media.enter_folder_name')"
                        icon="folder"
                        wire:keydown.enter="createFolder"
                        required
                        autofocus
                    />
                </div>
                <div class="modal-footer py-3">
                    <button type="button" wire:click="$set('showCreateFolderModal', false)" class="btn btn-ghost-secondary">
                        {{ __('core/media::media.cancel') }}
                    </button>
                    <button type="button" wire:click="createFolder" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="createFolder">
                            <i class="ti ti-plus me-1"></i>
                            {{ __('core/media::media.create') }}
                        </span>
                        <span wire:loading wire:target="createFolder">
                            <i class="ti ti-loader-2 icon-spin me-1"></i>
                            {{ __('core/media::media.creating') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
