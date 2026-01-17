{{-- Create Folder Modal --}}
@if($showCreateFolderModal)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        {!! tabler_icon('folder-plus', ['class' => 'icon text-primary']) !!}
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
                <div class="modal-footer">
                    <x-ui::button color="secondary" wire:click="$set('showCreateFolderModal', false')">
                        {{ __('core/media::media.cancel') }}
                    </x-ui::button>
                    <x-ui::button color="primary" wire:click="createFolder" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="createFolder">
                            {{ __('core/media::media.create') }}
                        </span>
                        <span wire:loading wire:target="createFolder">
                            {{ __('core/media::media.creating') }}
                        </span>
                    </x-ui::button>
                </div>
            </div>
        </div>
    </div>
@endif
