{{-- Create Folder Modal --}}
@if($showCreateFolderModal)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 justify-content-center">
                    <h5 class="modal-title fs-3 fw-bold mt-2">
                        {{ __('core/media::media.create_folder_new') }}
                    </h5>
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" wire:click="$set('showCreateFolderModal', false)"></button>
                </div>
                <div class="modal-body text-center pt-2 pb-4">
                    <div class="text-muted mb-4 small">{{ __('core/media::media.enter_folder_name') }}</div>

                    <div class="input-icon mb-2">
                        <span class="input-icon-addon">
                            {!! tabler_icon('folder', ['class' => 'icon icon-lg text-muted']) !!}
                        </span>
                        <input type="text"
                            class="form-control form-control-lg form-control-flush border-bottom fs-2 text-center"
                            wire:model="newFolderName"
                            placeholder="{{ __('core/media::media.folder_name') }}"
                            wire:keydown.enter="createFolder"
                            required
                            autofocus
                        >
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-ghost-secondary px-4 me-2" wire:click="$set('showCreateFolderModal', false')">
                        {{ __('core/media::media.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary px-5 rounded-pill" wire:click="createFolder" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="createFolder">
                            {!! tabler_icon('plus', ['class' => 'icon me-2']) !!} {{ __('core/media::media.create') }}
                        </span>
                        <div wire:loading wire:target="createFolder" class="spinner-border spinner-border-sm text-white" role="status"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
