@props([
    'key' => null,
    'config' => [],
    'value' => null,
])

@php
    $fileValue = $value;
    $imgSrc = null;
    $isImage = false;
    $isFullUrl = false;

    if ($fileValue instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
        try {
            $imgSrc = $fileValue->temporaryUrl();
            $isImage = true;
        } catch (\Exception $e) {
            $imgSrc = null;
        }
    } elseif (is_string($fileValue) && !empty($fileValue)) {
        $isFullUrl = str_starts_with($fileValue, 'http://') || str_starts_with($fileValue, 'https://');
        $imgSrc = $isFullUrl ? $fileValue : asset($fileValue);

        // Get extension (remove query string first)
        $urlPath = parse_url($fileValue, PHP_URL_PATH) ?? $fileValue;
        $fileExt = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'ico', 'svg', 'bmp'];
        $isImage = in_array($fileExt, $imageExts) || $isFullUrl;
    }

    $isLogoOrFavicon = in_array($key, ['logo', 'favicon']);
    $label = trans($config['label'] ?? '');
    $description = trans($config['description'] ?? '');
    $required = $config['required'] ?? false;
    $accept = $config['attributes']['accept'] ?? 'image/*';
@endphp

<div class="image-upload-field">
    @if($label)
        <label class="form-label d-block mb-2" for="setting-{{ $key }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    {{-- Image Upload Zone --}}
    <div class="image-upload-zone {{ $imgSrc ? 'has-image' : '' }}"
         x-data="{ isDragOver: false }"
         :class="{ 'drag-over': isDragOver }"
         @dragenter.prevent="isDragOver = true"
         @dragleave.prevent="isDragOver = false"
         @dragover.prevent
         @drop.prevent="isDragOver = false; $refs.imageInput?.click()"
         @click="$refs.imageInput?.click()"
         wire:loading.class="uploading"
         wire:target="settings.{{ $key }}">

        @if($imgSrc && $isImage)
            {{-- Image Preview --}}
            <div class="image-upload-preview">
                @if($isLogoOrFavicon)
                    <div class="preview-wrapper {{ $key === 'favicon' ? 'preview-favicon' : 'preview-logo' }}">
                        <img src="{{ $imgSrc }}"
                             alt="{{ $label }}"
                             class="preview-image">
                    </div>
                @else
                    <img src="{{ $imgSrc }}"
                         alt="{{ $label }}"
                         class="preview-image preview-default">
                @endif

                {{-- Overlay Actions --}}
                <div class="preview-overlay">
                    <div class="preview-actions">
                        <button type="button"
                                class="btn btn-sm btn-light"
                                @click.stop="$refs.imageInput?.click()">
                            <i class="ti ti-upload"></i>
                            {{ __('core/base::general.change') }}
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-danger"
                                wire:click="$set('settings.{{ $key }}', null)">
                            <i class="ti ti-trash"></i>
                            {{ __('core/settings::settings.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        @else
            {{-- Empty State / Upload Prompt --}}
            <div class="image-upload-empty">
                <div class="upload-icon">
                    <i class="ti ti-photo"></i>
                </div>
                <div class="upload-text text-center">
                    <p class="mb-0">{{ __('core/base::general.click_or_drag_image') }}</p>
                    @if($description)
                        <small class="text-muted">{{ $description }}</small>
                    @endif
                </div>
            </div>
        @endif

        {{-- Hidden File Input --}}
        <input type="file"
               x-ref="imageInput"
               class="d-none"
               id="setting-{{ $key }}"
               wire:model="settings.{{ $key }}"
               accept="{{ $accept }}"
               @if($required) required @endif>

        {{-- Loading Spinner --}}
        <div class="upload-loading-overlay" wire:loading wire:target="settings.{{ $key }}">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2 small fw-medium">{{ __('core/base::general.uploading') }}...</div>
        </div>
    </div>

    @error('settings.' . $key)
        <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror
</div>

@once
@push('styles')
<style>
    .image-upload-zone {
        border: 2px dashed var(--tblr-border-color);
        border-radius: var(--tblr-border-radius);
        background: var(--tblr-bg-surface);
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .image-upload-zone:hover {
        border-color: var(--tblr-primary);
        background: var(--tblr-primary-bg-subtle);
    }

    .image-upload-zone.drag-over {
        border-color: var(--tblr-primary);
        background: var(--tblr-primary-bg-subtle);
        transform: scale(1.01);
    }

    .image-upload-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        min-height: 160px;
        gap: 0.75rem;
    }

    .upload-icon {
        font-size: 2rem;
        color: var(--tblr-muted);
        opacity: 0.6;
    }

    .upload-text p {
        color: var(--tblr-body-color);
        font-weight: 500;
    }

    .image-upload-preview {
        position: relative;
        width: 100%;
        min-height: 160px;
    }

    .preview-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 1rem;
        background: var(--tblr-bg-surface-tertiary);
    }

    .preview-favicon {
        background: repeating-conic-gradient(#f0f0f0 0% 25%, #fff 0% 50%) 50% / 20px 20px;
    }

    .preview-favicon .preview-image,
    .preview-logo .preview-image {
        max-width: 100%;
        max-height: 120px;
        object-fit: contain;
    }

    .preview-default {
        width: 100%;
        height: auto;
    }

    .preview-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .image-upload-zone:hover .preview-overlay {
        opacity: 1;
    }

    .preview-actions {
        display: flex;
        gap: 0.5rem;
    }

    @media (max-width: 768px) {
        .image-upload-empty {
            padding: 1.5rem;
            min-height: 140px;
        }

        .preview-overlay {
            opacity: 1;
            background: rgba(0, 0, 0, 0.4);
        }

        .preview-actions {
            flex-direction: column;
            width: 100%;
            padding: 0 1rem;
        }

        .preview-actions .btn {
            width: 100%;
        }
    }
    .image-upload-zone.uploading {
        opacity: 0.7;
        pointer-events: none;
    }

    .upload-loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
</style>
@endpush
@endonce
