{{-- Create Folder Modal --}}
@if($showCreateFolderModal)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title">{{ __('Tạo Folder Mới') }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showCreateFolderModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Tên folder') }}</label>
                        <input type="text" wire:model="newFolderName" wire:keydown.enter="createFolder"
                               class="form-control" placeholder="{{ __('Nhập tên folder...') }}" autofocus>
                        @error('newFolderName')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" wire:click="$set('showCreateFolderModal', false)" class="btn btn-secondary">{{ __('Hủy') }}</button>
                    <button type="button" wire:click="createFolder" class="btn btn-primary">{{ __('Tạo') }}</button>
                </div>
            </div>
        </div>
    </div>
@endif
