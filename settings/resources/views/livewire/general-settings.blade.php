<div>
    @if (session()->has('success'))
        <x-ui::alert color="success" icon="check" dismiss="true" class="mb-3">
            {{ session('success') }}
        </x-ui::alert>
    @endif

    @if (session()->has('error'))
        <x-ui::alert color="danger" icon="alert-triangle" dismiss="true" class="mb-3">
            {{ session('error') }}
        </x-ui::alert>
    @endif

    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-8">
                <x-ui::card>
                    <x-slot name="header">
                        {{ tabler_icon('settings') }}
                        General Settings
                    </x-slot>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <x-form::input 
                                    wire:model="title" 
                                    label="Site Title"
                                    placeholder="Enter your site title"
                                    hint="This will be displayed in the browser title bar and search results"
                                />
                                @error('title') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Logo</label>
                                @if($logo)
                                    <div class="mb-2">
                                        <img src="{{ $logo }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                        <div class="text-muted small mt-1">Current logo</div>
                                    </div>
                                @endif
                                <input type="file" 
                                       wire:model="logoFile" 
                                       class="form-control @error('logoFile') is-invalid @enderror"
                                       accept="image/*">
                                <div class="form-hint">Upload a new logo (max 2MB). Recommended size: 200x60px</div>
                                @error('logoFile') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                                
                                @if($logoFile)
                                    <div class="mt-2">
                                        <img src="{{ $logoFile->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="max-height: 100px;">
                                        <div class="text-muted small mt-1">Preview</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Favicon</label>
                                @if($favicon)
                                    <div class="mb-2">
                                        <img src="{{ $favicon }}" alt="Current Favicon" class="img-thumbnail" style="max-height: 32px;">
                                        <div class="text-muted small mt-1">Current favicon</div>
                                    </div>
                                @endif
                                <input type="file" 
                                       wire:model="faviconFile" 
                                       class="form-control @error('faviconFile') is-invalid @enderror"
                                       accept="image/*">
                                <div class="form-hint">Upload a new favicon (max 1MB). Recommended size: 32x32px</div>
                                @error('faviconFile') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                                
                                @if($faviconFile)
                                    <div class="mt-2">
                                        <img src="{{ $faviconFile->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="max-height: 32px;">
                                        <div class="text-muted small mt-1">Preview</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <small>Changes will be applied immediately after saving.</small>
                            </div>
                            <div>
                                <button type="button" wire:click="loadSettings" class="btn btn-outline-secondary me-2">
                                    {{ tabler_icon('refresh') }}
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        {{ tabler_icon('device-floppy') }}
                                        Save Settings
                                    </span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Saving...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </x-slot>
                </x-ui::card>
            </div>

            <div class="col-md-4">
                <x-ui::card>
                    <x-slot name="header">
                        {{ tabler_icon('info-circle') }}
                        Information
                    </x-slot>

                    <div class="mb-3">
                        <h4 class="card-title">Site Title</h4>
                        <p class="text-secondary">The site title appears in browser tabs, search results, and social media shares.</p>
                    </div>

                    <div class="mb-3">
                        <h4 class="card-title">Logo</h4>
                        <p class="text-secondary">Your site logo will be displayed in the navigation bar. For best results, use a PNG or SVG file.</p>
                    </div>

                    <div class="mb-3">
                        <h4 class="card-title">Favicon</h4>
                        <p class="text-secondary">The favicon is the small icon that appears in browser tabs and bookmarks.</p>
                    </div>
                </x-ui::card>
            </div>
        </div>
    </form>
</div>
