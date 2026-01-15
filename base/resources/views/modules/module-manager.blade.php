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

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center gap-3">
                <span class="avatar avatar-sm bg-primary-lt">
                    {!! tabler_icon('package', ['class' => 'icon']) !!}
                </span>
                <h3 class="card-title mb-0">{{ __('core/base::general.module_manager') }}</h3>
            </div>
            <div class="card-actions">
                <x-ui::button color="primary" icon="refresh" wire:click="discover" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="discover">
                        {{ __('core/base::general.scan_modules') }}
                    </span>
                    <span wire:loading wire:target="discover">
                        <span class="spinner-border spinner-border-sm me-1"></span> {{ __('core/base::general.scanning') }}
                    </span>
                </x-ui::button>
            </div>
        </div>
        <div class="card-body">
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
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>{{ __('core/base::general.module') }}</th>
                                <th>{{ __('core/base::general.version') }}</th>
                                <th>{{ __('core/base::general.status') }}</th>
                                <th class="w-1">{{ __('core/base::general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-blue-lt me-2">
                                                <i class="ti ti-package"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-medium">{{ $module->display_name }}</div>
                                                <div class="text-muted small">{{ $module->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-azure-lt">{{ $module->version ?? '1.0.0' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $module->status_color }}-lt">
                                            {{ $module->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            @if($module->status === 'pending')
                                                <x-ui::button color="success" size="sm" icon="download"
                                                        wire:click="install('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        title="{{ __('core/base::general.install_module') }}">
                                                    {{ __('core/base::general.install') }}
                                                </x-ui::button>
                                            @elseif($module->status === 'installed')
                                                <x-ui::button color="primary" size="sm" icon="player-play"
                                                        wire:click="enable('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        title="{{ __('core/base::general.activate_module') }}">
                                                    {{ __('core/base::general.activate') }}
                                                </x-ui::button>
                                            @elseif($module->status === 'active')
                                                <x-ui::button color="warning" size="sm" icon="player-pause"
                                                        wire:click="disable('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        title="{{ __('core/base::general.disable_module') }}">
                                                    {{ __('core/base::general.disable') }}
                                                </x-ui::button>
                                            @elseif($module->status === 'disabled')
                                                <x-ui::button color="primary" size="sm" icon="player-play"
                                                        wire:click="enable('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        title="{{ __('core/base::general.reactivate_module') }}">
                                                    {{ __('core/base::general.reactivate') }}
                                                </x-ui::button>
                                            @endif

                                            @if($module->status !== 'pending')
                                                <x-ui::button color="danger" size="sm" icon="trash" :outline="true"
                                                        wire:click="uninstall('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        wire:confirm="{{ __('core/base::general.confirm_uninstall') }}"
                                                        title="{{ __('core/base::general.uninstall_module') }}">
                                                    {{ __('core/base::general.remove') }}
                                                </x-ui::button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Module Info Modal -->
    @if($selectedModule)
        <x-ui::modal id="module-info-modal" :header="$selectedModule->display_name" class="modal-lg">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('core/base::general.name') }}</label>
                        <div class="form-control-plaintext">{{ $selectedModule->name }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('core/base::general.version') }}</label>
                        <div class="form-control-plaintext">{{ $selectedModule->version ?? '1.0.0' }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('core/base::general.description') }}</label>
                        <div class="form-control-plaintext">{{ $selectedModule->description ?? __('core/base::general.no_description') }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('core/base::general.namespace') }}</label>
                        <div class="form-control-plaintext"><code>{{ $selectedModule->namespace }}</code></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('core/base::general.provider') }}</label>
                        <div class="form-control-plaintext"><code>{{ $selectedModule->provider }}</code></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('core/base::general.folder_path') }}</label>
                        <div class="form-control-plaintext"><code>{{ $selectedModule->path }}</code></div>
                    </div>
                </div>
            </div>
        </x-ui::modal>
    @endif
</div>
