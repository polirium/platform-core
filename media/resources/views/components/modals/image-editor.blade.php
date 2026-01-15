{{-- Image Editor Modal --}}
@if($showImageEditor && $editingImage)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.85);"
         x-data="imageEditor({{ $editingImageId }}, '{{ $editingImage->getUrl() }}?t={{ now()->timestamp }}')"
         x-init="init()">
        <div class="modal-dialog modal-xl modal-dialog-centered" @click.stop>
            <div class="modal-content">
                <div class="modal-header py-3">
                    <h5 class="modal-title">
                        <i class="ti ti-photo me-2"></i>
                        {{ __('core/media::media.edit_image') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeImageEditor" @click="destroy()"></button>
                </div>

                <div class="modal-body">
                    {{-- Messages --}}
                    <template x-if="message">
                        <div :class="'alert alert-' + messageType + ' py-2 mb-3'" x-text="message"></div>
                    </template>
                    @if(session('success'))
                        <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger py-2 mb-3">{{ session('error') }}</div>
                    @endif

                    <div class="row">
                        {{-- Image Preview with Cropper --}}
                        <div class="col-md-8">
                            <div class="image-preview-area rounded" style="background: #1a1a1a; height: 550px;">
                                <div style="height: 550px; overflow: hidden;">
                                    <img x-ref="cropperImage"
                                         :src="imageUrl"
                                         style="max-width: 100%; display: block;">
                                </div>
                            </div>

                            {{-- Crop Info Bar --}}
                            <div class="d-flex justify-content-between align-items-center mt-2 px-2">
                                <div class="text-muted small">
                                    {{ $editingImage->name }}
                                </div>
                                <div class="text-muted small">
                                    Crop: <strong x-text="cropData.width + ' x ' + cropData.height"></strong> px
                                </div>
                            </div>
                        </div>

                        {{-- Tools Panel --}}
                        <div class="col-md-4">
                            <div class="tools-panel p-3 rounded" style="background: var(--tblr-bg-surface-secondary);">

                                {{-- Crop Section --}}
                                <div class="tool-section">
                                    <div class="tool-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.13 1L6 16a2 2 0 0 0 2 2h15"/><path d="M1 6.13L16 6a2 2 0 0 1 2 2v15"/></svg>
                                        {{ __('core/media::media.crop_image') }}
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small text-muted">{{ __('core/media::media.aspect_ratio') }}</label>
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="setAspectRatio(0)">{{ __('core/media::media.free') }}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="setAspectRatio(1)">1:1</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="setAspectRatio(16/9)">16:9</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="setAspectRatio(4/3)">4:3</button>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" @click="resetCrop()">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2.5 2v6h6M21.5 22v-6h-6"/><path d="M22 11.5A10 10 0 0 0 3.2 7.2M2 12.5a10 10 0 0 0 18.8 4.2"/></svg>
                                            {{ __('core/media::media.reset') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary flex-fill" @click="applyCrop()" :disabled="loading">
                                            <span x-show="!loading">{{ __('core/media::media.apply_crop') }}</span>
                                            <span x-show="loading" class="spinner-border spinner-border-sm"></span>
                                        </button>
                                    </div>
                                </div>

                                <hr class="my-3">

                                {{-- Resize Section --}}
                                <div class="tool-section">
                                    <div class="tool-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                                        {{ __('core/media::media.resize_image') }}
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <x-ui.form.input
                                                wire:model="resizeWidth"
                                                :label="__('core/media::media.width')"
                                                type="number"
                                                :placeholder="__('core/media::media.width')"
                                            />
                                        </div>
                                        <div class="col-6">
                                            <x-ui.form.input
                                                wire:model="resizeHeight"
                                                :label="__('core/media::media.height')"
                                                type="number"
                                                :placeholder="__('core/media::media.height')"
                                            />
                                        </div>
                                    </div>
                                    <button type="button" wire:click="resizeImage" class="btn btn-sm btn-outline-primary w-100" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="resizeImage">
                                            <i class="ti ti-arrows-minimize me-1"></i>
                                            {{ __('core/media::media.resize') }}
                                        </span>
                                        <span wire:loading wire:target="resizeImage">
                                            <i class="ti ti-loader-2 icon-spin me-1"></i>
                                            {{ __('core/media::media.resizing') }}
                                        </span>
                                    </button>
                                </div>

                                <hr class="my-3">

                                {{-- Rotate & Flip Section --}}
                                <div class="tool-section">
                                    <div class="tool-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38"/></svg>
                                        {{ __('core/media::media.rotate_flip') }}
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small text-muted">{{ __('core/media::media.rotate') }}</label>
                                        <div class="btn-group w-100">
                                            <button type="button" wire:click="rotateImage(-90)" class="btn btn-sm btn-outline-secondary">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
                                                {{ __('core/media::media.rotate_left') }}
                                            </button>
                                            <button type="button" wire:click="rotateImage(90)" class="btn btn-sm btn-outline-secondary">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38"/></svg>
                                                {{ __('core/media::media.rotate_right') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label small text-muted">{{ __('core/media::media.flip') }}</label>
                                        <div class="btn-group w-100">
                                            <button type="button" wire:click="flipImage('h')" class="btn btn-sm btn-outline-secondary">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h3"/><path d="M16 3h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-3"/><path d="M12 20v2"/><path d="M12 14v2"/><path d="M12 8v2"/><path d="M12 2v2"/></svg>
                                                {{ __('core/media::media.flip_horizontal') }}
                                            </button>
                                            <button type="button" wire:click="flipImage('v')" class="btn btn-sm btn-outline-secondary">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 8V5a2 2 0 0 1 2-2h14c1.1 0 2 .9 2 2v3"/><path d="M3 16v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3"/><path d="M4 12H2"/><path d="M10 12H8"/><path d="M16 12h-2"/><path d="M22 12h-2"/></svg>
                                                {{ __('core/media::media.flip_vertical') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-3">
                    <button type="button" wire:click="closeImageEditor" @click="destroy()" class="btn btn-ghost-secondary">
                        <i class="ti ti-x me-1"></i>
                        {{ __('core/media::media.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
