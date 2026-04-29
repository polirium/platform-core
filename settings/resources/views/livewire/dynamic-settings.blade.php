<div class="settings-form">
    <form wire:submit.prevent="save">
        @if($successMessage)
            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                <div class="d-flex">
                    <div>
                        <i class="ti ti-check alert-icon"></i>
                        {{ $successMessage }}
                    </div>
                    <a href="#" class="alert-close" data-bs-dismiss="alert"></a>
                </div>
            </div>
        @endif

        @if(!empty($settingDefs))
            {{-- Special handling: Group logo and favicon in 2 columns --}}
            @php
                $logoConfig = $settingDefs['logo'] ?? null;
                $faviconConfig = $settingDefs['favicon'] ?? null;
                $hasBothImages = $logoConfig && $faviconConfig;
            @endphp

            <div class="row g-4">
                @if($hasBothImages)
                    {{-- Logo + Favicon in 2 columns --}}
                    <div class="col-12">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <x-form::image-upload
                                    key="logo"
                                    :config="$logoConfig"
                                    :value="$this->getSettingValue('logo')" />
                            </div>
                            <div class="col-md-6">
                                <x-form::image-upload
                                    key="favicon"
                                    :config="$faviconConfig"
                                    :value="$this->getSettingValue('favicon')" />
                            </div>
                        </div>
                    </div>
                @endif

                @foreach($settingDefs as $key => $config)
                    {{-- Skip logo and favicon if already rendered above --}}
                    @if($hasBothImages && in_array($key, ['logo', 'favicon']))
                        @continue
                    @endif

                    <div class="@if(($config['type'] ?? 'text') === 'textarea') col-12 @else col-md-6 @endif">
                        @if($config['type'] === 'checkbox')
                            <div class="mb-3 form-switch">
                                <input type="checkbox"
                                       class="form-input"
                                       id="{{ $key }}"
                                       wire:model="settings.{{ $key }}"
                                       @if($config['required'] ?? false) required @endif>
                                <label class="form-check-label" for="{{ $key }}">
                                    @if($config['label'] ?? false)
                                        {{ trans($config['label']) }}
                                        @if($config['required'] ?? false)
                                            <span class="text-danger">*</span>
                                        @endif
                                    @endif
                                </label>
                                @if($config['description'] ?? false)
                                    <p class="form-text text-muted">{{ trans($config['description']) }}</p>
                                @endif
                            </div>
                        @elseif($config['type'] === 'textarea')
                            @if($config['label'] ?? false)
                                <label class="form-label d-block mb-2" for="{{ $key }}">
                                    {{ trans($config['label']) }}
                                    @if($config['required'] ?? false)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                            @endif
                            <textarea class="form-control"
                                      id="{{ $key }}"
                                      wire:model="settings.{{ $key }}"
                                      rows="3"
                                      @if($config['required'] ?? false) required @endif>{{ $this->getSettingValue($key) }}</textarea>
                            @if($config['description'] ?? false)
                                <p class="form-text text-muted">{{ trans($config['description']) }}</p>
                            @endif
                            @error('settings.' . $key)
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        @elseif($config['type'] === 'select')
                            @if($config['label'] ?? false)
                                <label class="form-label d-block mb-2" for="{{ $key }}">
                                    {{ trans($config['label']) }}
                                    @if($config['required'] ?? false)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                            @endif
                            <select class="form-select"
                                    id="{{ $key }}"
                                    wire:model.live="settings.{{ $key }}"
                                    @if($config['required'] ?? false) required @endif>
                                <option value="">{{ __('core/settings::settings.select_placeholder') }}</option>
                                @if(isset($config['options']) && is_array($config['options']))
                                    @foreach($config['options'] as $value => $label)
                                        <option value="{{ $value }}" @if($this->getSettingValue($key) == $value) selected @endif>
                                            {{ trans($label) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if($config['description'] ?? false)
                                <p class="form-text text-muted">{{ trans($config['description']) }}</p>
                            @endif
                            @error('settings.' . $key)
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        @elseif($config['type'] === 'file')
                            <x-form::image-upload
                                key="{{ $key }}"
                                :config="$config"
                                :value="$this->getSettingValue($key)" />
                        @else
                            {{-- Default: text, number, email, url, password input --}}
                            @if($config['label'] ?? false)
                                <label class="form-label d-block mb-2" for="{{ $key }}">
                                    {{ trans($config['label']) }}
                                    @if($config['required'] ?? false)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                            @endif
                            <input type="{{ $config['type'] ?? 'text' }}"
                                   class="form-control"
                                   id="{{ $key }}"
                                   wire:model.live="settings.{{ $key }}"
                                   @if($config['required'] ?? false) required @endif
                                   @if(isset($config['attributes']))
                                       @foreach($config['attributes'] as $attrKey => $attrValue)
                                           {{ $attrKey }}="{{ $attrValue }}"
                                       @endforeach
                                   @endif
                                   value="{{ $this->getSettingValue($key) }}">
                            @if($config['description'] ?? false)
                                <p class="form-text text-muted">{{ trans($config['description']) }}</p>
                            @endif
                            @error('settings.' . $key)
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Save button (bottom) --}}
        <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
            <button type="button"
                    class="btn btn-outline-secondary"
                    wire:click="loadSettings">
                <i class="ti ti-refresh"></i>
                {{ __('core/settings::settings.cancel') }}
            </button>
            <button type="submit"
                    class="btn btn-primary"
                    wire:loading.attr="disabled">
                <i class="ti ti-device-floppy"></i>
                <span wire:loading.remove wire:target="save">{{ __('core/settings::settings.save_settings') }}</span>
                <span wire:loading wire:target="save">{{ __('core/settings::settings.saving') }}</span>
            </button>
        </div>
    </form>
</div>
