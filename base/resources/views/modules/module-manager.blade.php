<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="page-title mb-1">{{ __('core/base::general.modules') }}</h2>
            <p class="text-muted small mb-0">{{ __('Quản lý và kích hoạt/vô hiệu hóa các module hệ thống') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary" wire:click="$dispatch('open-upload-modal')">
                {!! tabler_icon('upload', ['class' => 'icon']) !!}
                {{ __('Upload Module') }}
            </button>
            <button class="btn btn-dark" wire:click="discover">
                {!! tabler_icon('refresh', ['class' => 'icon']) !!}
                {{ __('Refresh') }}
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-3">
        <div class="card-body p-2">
            <div class="d-flex align-items-center justify-content-between">
                {{-- Bulk Actions --}}
                <div class="d-flex align-items-center gap-2">
                    @if(count($selected) > 0)
                        <div class="dropdown">
                            <button class="btn btn-ghost-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                {{ __('Đã chọn') }} ({{ count($selected) }})
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" wire:click.prevent="bulkActivate">
                                        {!! tabler_icon('check', ['class' => 'icon icon-sm me-2 text-success']) !!}
                                        {{ __('Kích hoạt') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" wire:click.prevent="bulkDeactivate">
                                        {!! tabler_icon('x', ['class' => 'icon icon-sm me-2 text-warning']) !!}
                                        {{ __('Vô hiệu hóa') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @else
                        <span class="text-muted ms-2 small">{{ count($modules) }} {{ __('modules') }}</span>
                    @endif
                </div>

                {{-- Search & View Toggle --}}
                <div class="d-flex align-items-center gap-2">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            {!! tabler_icon('search', ['class' => 'icon']) !!}
                        </span>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="{{ __('Search...') }}"
                            wire:model.live.debounce.300ms="search"
                        >
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-icon {{ $viewMode === 'list' ? 'btn-dark active' : 'btn-ghost-secondary' }}" wire:click="setViewMode('list')">
                            {!! tabler_icon('list', ['class' => 'icon']) !!}
                        </button>
                        <button class="btn btn-icon {{ $viewMode === 'grid' ? 'btn-dark active' : 'btn-ghost-secondary' }}" wire:click="setViewMode('grid')">
                            {!! tabler_icon('layout-grid', ['class' => 'icon']) !!}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($modules->isEmpty())
         <div class="empty">
            <div class="empty-icon">
                <i class="ti ti-package-off" style="font-size: 3rem;"></i>
            </div>
            <p class="empty-title">{{ __('core/base::general.no_modules') }}</p>
            <p class="empty-subtitle text-muted">
                {{ __('core/base::general.add_module_and_scan') }}
            </p>
        </div>
    @else
        @if($viewMode === 'list')
            {{-- LIST VIEW --}}
            <div class="card">
                <div class="list-group list-group-flush">
                    {{-- Header Row --}}
                    <div class="list-group-item bg-dark-lt d-none d-md-block py-2">
                        <div class="row align-items-center text-uppercase small text-muted font-weight-bold">
                            <div class="col-auto w-1"></div> {{-- Checkbox spacer --}}
                            <div class="col-4">{{ __('MODULE') }}</div>
                            <div class="col-4">{{ __('DESCRIPTION') }}</div>
                            <div class="col-1">{{ __('STATUS') }}</div>
                            <div class="col-2">{{ __('VERSION') }}</div>
                            <div class="col-auto ms-auto">{{ __('ACTIONS') }}</div>
                        </div>
                    </div>

                    @foreach($modules as $module)
                        <div class="list-group-item p-3 {{ in_array($module->name, $selected) ? 'bg-indigo-lt' : '' }}">
                            <div class="row align-items-center">
                                {{-- Checkbox --}}
                                <div class="col-auto">
                                    <input class="form-check-input" type="checkbox" wire:model.live="selected" value="{{ $module->name }}">
                                </div>

                                {{-- Module Info --}}
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-md bg-dark-lt rounded me-3" style="{{ $module->image ? 'background-image: url('.$module->image.')' : '' }}">
                                            @if(!$module->image)
                                                {!! tabler_icon('package', ['class' => 'icon icon-lg text-white']) !!}
                                            @endif
                                        </span>
                                        <div>
                                            <div class="font-weight-bold d-flex align-items-center gap-2">
                                                {{ $module->display_name }}
                                            </div>
                                            <div class="text-muted small mt-1">{{ $module->namespace }}</div>

                                            {{-- Inline Actions --}}
                                            <div class="mt-2 small">
                                                @if($module->isActive())
                                                    <a href="#" class="text-warning me-2 text-decoration-none" wire:click.prevent="disable('{{ $module->name }}')">
                                                        {{ __('Deactivate') }}
                                                    </a>
                                                @else
                                                    <a href="#" class="text-success me-2 text-decoration-none" wire:click.prevent="enable('{{ $module->name }}')">
                                                        {{ __('Activate') }}
                                                    </a>
                                                @endif

                                                <span class="text-secondary opacity-25">|</span>

                                                <a href="#" class="text-info mx-2 text-decoration-none" wire:click.prevent="download('{{ $module->name }}')">
                                                    {{ __('Download') }}
                                                </a>

                                                <span class="text-secondary opacity-25">|</span>

                                                <a href="#" class="text-danger ms-2 text-decoration-none" wire:click.prevent="delete('{{ $module->name }}')" wire:confirm="{{ __('core/base::general.confirm_uninstall') }}">
                                                    {{ __('Delete') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="col-md-4">
                                    <p class="text-muted small mb-0 text-truncate" style="max-width: 350px;" title="{{ $module->description }}">
                                        {{ $module->description ?? __('No description') }}
                                    </p>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-1">
                                    @if($module->isActive())
                                        <span class="badge bg-success-lt">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary-lt">{{ __('Inactive') }}</span>
                                    @endif
                                </div>

                                {{-- Version & Author --}}
                                <div class="col-md-2">
                                    <div class="font-weight-medium">{{ $module->version ?? '1.0.0' }}</div>
                                    <div class="text-muted small">{{ __('By') }} {{ $module->author ?? 'Unknown' }}</div>
                                </div>

                                {{-- Toggle Switch --}}
                                <div class="col-auto ms-auto">
                                    <label class="form-check form-switch m-0">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            {{ $module->isActive() ? 'checked' : '' }}
                                            wire:click="toggleStatus('{{ $module->name }}')"
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- GRID VIEW --}}
            <div class="row row-cards">
                @foreach($modules as $module)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-stacked">
                            @if($module->image)
                                <div class="img-responsive img-responsive-21x9 card-img-top" style="background-image: url({{ $module->image }})"></div>
                            @endif
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    @if(!$module->image)
                                        <span class="avatar me-3 bg-blue-lt">
                                            {!! tabler_icon('package', ['class' => 'icon']) !!}
                                        </span>
                                    @endif
                                    <div>
                                        <h3 class="card-title mb-1">{{ $module->display_name }}</h3>
                                        <div class="text-muted small">{{ $module->namespace }}</div>
                                    </div>
                                    <div class="ms-auto">
                                        <label class="form-check form-switch m-0">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                {{ $module->isActive() ? 'checked' : '' }}
                                                wire:click="toggleStatus('{{ $module->name }}')"
                                            >
                                        </label>
                                    </div>
                                </div>
                                <div class="text-muted text-truncate mb-3" title="{{ $module->description }}">
                                    {{ $module->description ?? __('No description') }}
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="text-muted small">
                                        {{ __('v') }}{{ $module->version ?? '1.0.0' }} &bull; {{ $module->author ?? 'Polyx' }}
                                    </div>
                                    <div class="ms-auto">
                                        @if($module->isActive())
                                            <span class="badge bg-success-lt">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-secondary-lt">{{ __('Inactive') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex">
                                    @if($module->isActive())
                                        <a href="#" class="btn btn-link link-secondary" wire:click.prevent="disable('{{ $module->name }}')">{{ __('Deactivate') }}</a>
                                    @else
                                        <a href="#" class="btn btn-link link-primary" wire:click.prevent="enable('{{ $module->name }}')">{{ __('Activate') }}</a>
                                    @endif
                                    <a href="#" class="btn btn-link link-danger ms-auto" wire:click.prevent="delete('{{ $module->name }}')" wire:confirm="{{ __('core/base::general.confirm_uninstall') }}">{{ __('Delete') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- Upload Modal --}}
    <x-ui::modal id="upload-module-modal" :title="__('Upload Module')" class="modal-dialog-centered">
        <form wire:submit.prevent="uploadModule">
            <div class="mb-3">
                <label class="form-label">{{ __('Select Module ZIP file') }}</label>
                <input type="file" class="form-control" wire:model="moduleFile" accept=".zip">
                @error('moduleFile') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="text-muted small mb-3">
                <p class="mb-1">{{ __('Instructions:') }}</p>
                <ul class="ps-3 mb-0">
                    <li>{{ __('File type must be .zip') }}</li>
                    <li>{{ __('Maximum size: 50MB') }}</li>
                    <li>{{ __('Structure: The ZIP should contain the module folder at the root.') }}</li>
                </ul>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading wire:target="uploadModule" class="spinner-border spinner-border-sm me-1"></span>
                    {{ __('Upload & Install') }}
                </button>
            </div>
        </form>
    </x-ui::modal>
</div>
