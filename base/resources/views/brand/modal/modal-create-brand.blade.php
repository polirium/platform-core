@php
    $isEdit = !empty($brand_id);
    $modalTitle = $isEdit
        ? __('core/base::general.edit_brand_text') . ' ' . __('core/base::general.brand_text')
        : __('core/base::general.create_brand_text') . ' ' . __('core/base::general.brand_text');
@endphp

<div>
    <form wire:submit.prevent="save">
        <x-ui::modal
            id="modal-create-brand"
            :header="$modalTitle"
            size="modal-lg"
        >
            <div class="brand-modal-wrapper">
                {{-- Hero Section - Improved spacing --}}
                <div class="brand-modal-hero">
                    <div class="brand-modal-icon-box">
                        <svg class="brand-modal-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4.5" y="6.75" width="3" height="3.75" rx="0.75" fill="currentColor" fill-opacity="0.2"/>
                            <rect x="16.5" y="6.75" width="3" height="3.75" rx="0.75" fill="currentColor" fill-opacity="0.2"/>
                            <rect x="10.5" y="6.75" width="3" height="3.75" rx="0.75"/>
                            <rect x="16.5" y="14.25" width="3" height="3.75" rx="0.75"/>
                            <rect x="10.5" y="14.25" width="3" height="3.75" rx="0.75"/>
                        </svg>
                    </div>
                    <div class="brand-modal-text">
                        <h3 class="brand-modal-heading">
                            {{ $isEdit ? __('Cập nhật thương hiệu') : __('Tạo thương hiệu mới') }}
                        </h3>
                        <p class="brand-modal-subheading">
                            {{ $isEdit
                                ? __('Chỉnh sửa thông tin thương hiệu của bạn')
                                : __('Nhập thông tin để tạo thương hiệu mới')
                            }}
                        </p>
                    </div>
                </div>

                {{-- Form Section - Better spacing and layout --}}
                <div class="brand-modal-form-section">
                    {{-- Brand Name --}}
                    <div class="brand-form-field">
                        <label for="brand-name" class="brand-form-label">
                            <span class="brand-label-content">
                                <svg class="brand-label-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                                    <line x1="9" y1="12" x2="15" y2="12"/>
                                </svg>
                                {{ __('core/base::general.brand_name') }}
                            </span>
                            <span class="brand-required-mark">*</span>
                        </label>
                        <div class="brand-input-group">
                            <input
                                id="brand-name"
                                type="text"
                                wire:model="name"
                                class="brand-form-input"
                                placeholder="{{ __('core/base::general.enter_brand_name') }}"
                                required
                                autofocus
                                autocomplete="off"
                            />
                            <div class="brand-input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7.5 8.25h9M7.5 12h9m-9 3.75h9M7.5 5.25C7.5 4.83579 7.83579 4.5 8.25 4.5h7.5C16.1642 4.5 16.5 4.83579 16.5 5.25v14.25c0 .4142-.3358.75-.75.75H8.25c-.41421 0-.75-.3358-.75-.75V5.25Z"/>
                                </svg>
                            </div>
                        </div>
                        @error('name')
                            <p class="brand-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Note --}}
                    <div class="brand-form-field">
                        <label for="brand-note" class="brand-form-label">
                            <span class="brand-label-content">
                                <svg class="brand-label-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{ __('core/base::general.note_english') }}
                            </span>
                            <span class="brand-optional-mark">{{ __('Tùy chọn') }}</span>
                        </label>
                        <div class="brand-input-group">
                            <textarea
                                id="brand-note"
                                wire:model="note"
                                class="brand-form-textarea"
                                rows="3"
                                placeholder="{{ __('core/base::general.enter_note') }}"
                            >{{ $note }}</textarea>
                            <div class="brand-textarea-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="brand-modal-footer">
                    <button
                        type="button"
                        class="brand-modal-btn brand-btn-cancel"
                        data-bs-dismiss="modal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                        {{ __('core/base::general.cancel') }}
                    </button>
                    <button
                        type="submit"
                        class="brand-modal-btn brand-btn-submit"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="save">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('core/base::general.save') }}
                        </span>
                        <span wire:loading wire:target="save">
                            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                <path d="M12 8v4l3 3"/>
                            </svg>
                            {{ __('core/base::general.saving') }}
                        </span>
                    </button>
                </div>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
