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
            <p class="text-muted small mb-0">{{ trans('core/base::general.module_manager_desc') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @can('modules.manage')
            <button class="btn btn-primary" wire:click="$dispatch('open-upload-modal')">
                {!! tabler_icon('upload', ['class' => 'icon']) !!}
                {{ trans('core/base::general.upload_module') }}
            </button>
            <button class="btn btn-dark" wire:click="discover">
                {!! tabler_icon('refresh', ['class' => 'icon']) !!}
                {{ trans('core/base::general.refresh') }}
            </button>
            @endcan
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-3">
        <div class="card-body p-2">
            <div class="d-flex align-items-center justify-content-between">
                {{-- Bulk Actions --}}
                <div class="d-flex align-items-center gap-2">
                    @if(count($selected) > 0)
                        @can('modules.manage')
                        <div class="dropdown">
                            <button class="btn btn-ghost-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                {{ trans('core/base::general.selected') }} ({{ count($selected) }})
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" wire:click.prevent="bulkActivate">
                                        {!! tabler_icon('check', ['class' => 'icon icon-sm me-2 text-success']) !!}
                                        {{ trans('core/base::general.activate') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" wire:click.prevent="bulkDeactivate">
                                        {!! tabler_icon('x', ['class' => 'icon icon-sm me-2 text-warning']) !!}
                                        {{ trans('core/base::general.disable') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endcan
                    @else
                        <span class="text-muted ms-2 small">{{ count($modules) }} {{ trans('core/base::general.modules') }}</span>
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
                            placeholder="{{ trans('core/base::general.search_placeholder') }}"
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
                            <div class="col-4">{{ trans('core/base::general.module') }}</div>
                            <div class="col-4">{{ trans('core/base::general.description') }}</div>
                            <div class="col-1">{{ trans('core/base::general.status') }}</div>
                            <div class="col-2">{{ trans('core/base::general.version') }}</div>
                            <div class="col-auto ms-auto">{{ trans('core/base::general.actions') }}</div>
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
                                                @can('modules.manage')
                                                @if($module->isActive())
                                                    <a href="#" class="text-warning me-2 text-decoration-none" wire:click.prevent="disable('{{ $module->name }}')">
                                                        {{ trans('core/base::general.deactivate') }}
                                                    </a>
                                                @else
                                                    <a href="#" class="text-success me-2 text-decoration-none" wire:click.prevent="enable('{{ $module->name }}')">
                                                        {{ trans('core/base::general.activate') }}
                                                    </a>
                                                @endif

                                                <span class="text-secondary opacity-25">|</span>

                                                <a href="#" class="text-info mx-2 text-decoration-none" wire:click.prevent="download('{{ $module->name }}')">
                                                    {{ trans('core/base::general.download') }}
                                                </a>

                                                <span class="text-secondary opacity-25">|</span>

                                                <a href="#" class="text-danger ms-2 text-decoration-none" wire:click.prevent="delete('{{ $module->name }}')" wire:confirm="{{ __('core/base::general.confirm_uninstall') }}">
                                                    {{ trans('core/base::general.delete') }}
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="col-md-4">
                                    <p class="text-muted small mb-0 text-truncate" style="max-width: 350px;" title="{{ $module->description }}">
                                        {{ $module->description ?? trans('core/base::general.no_description') }}
                                    </p>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-1">
                                    @if($module->isActive())
                                        <span class="badge bg-success-lt">{{ trans('core/base::general.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary-lt">{{ trans('core/base::general.inactive') }}</span>
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
                                    {{ $module->description ?? trans('core/base::general.no_description') }}
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="text-muted small">
                                        {{ __('v') }}{{ $module->version ?? '1.0.0' }} &bull; {{ $module->author ?? 'Polyx' }}
                                    </div>
                                    <div class="ms-auto">
                                        @if($module->isActive())
                                            <span class="badge bg-success-lt">{{ trans('core/base::general.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary-lt">{{ trans('core/base::general.inactive') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex">
                                    @can('modules.manage')
                                    @if($module->isActive())
                                        <a href="#" class="btn btn-link link-secondary" wire:click.prevent="disable('{{ $module->name }}')">{{ trans('core/base::general.deactivate') }}</a>
                                    @else
                                        <a href="#" class="btn btn-link link-primary" wire:click.prevent="enable('{{ $module->name }}')">{{ trans('core/base::general.activate') }}</a>
                                    @endif
                                    <a href="#" class="btn btn-link link-danger ms-auto" wire:click.prevent="delete('{{ $module->name }}')" wire:confirm="{{ __('core/base::general.confirm_uninstall') }}">{{ trans('core/base::general.delete') }}</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- Upload Modal --}}
    <x-ui::modal id="upload-module-modal" :title="trans('core/base::general.upload_module')" class="modal-dialog-centered">
        <form wire:submit.prevent="uploadModule">
            <div class="mb-3">
                <label class="form-label">{{ trans('core/base::general.select_module_zip') }}</label>
                <input type="file" class="form-control" wire:model="moduleFile" accept=".zip">
                @error('moduleFile') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="text-muted small mb-3">
                <p class="mb-1">{{ trans('core/base::general.instructions') }}</p>
                <ul class="ps-3 mb-0">
                    <li>{{ trans('core/base::general.file_type_zip') }}</li>
                    <li>{{ trans('core/base::general.max_size_50mb') }}</li>
                    <li>{{ trans('core/base::general.zip_structure_hint') }}</li>
                </ul>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">{{ trans('core/base::general.cancel') }}</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading wire:target="uploadModule" class="spinner-border spinner-border-sm me-1"></span>
                    {{ trans('core/base::general.upload_install') }}
                </button>
            </div>
        </form>
    </x-ui::modal>
</div>
