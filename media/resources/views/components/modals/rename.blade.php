{{-- Rename Modal --}}
@if($showRenameModal)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title">{{ __('Đổi tên') }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showRenameModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Tên mới') }}</label>
                        <input type="text" wire:model="renameItemName" wire:keydown.enter="renameItem"
                               class="form-control" autofocus>
                        @error('renameItemName')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" wire:click="$set('showRenameModal', false)" class="btn btn-secondary">{{ __('Hủy') }}</button>
                    <button type="button" wire:click="renameItem" class="btn btn-primary">{{ __('Lưu') }}</button>
                </div>
            </div>
        </div>
    </div>
@endif
