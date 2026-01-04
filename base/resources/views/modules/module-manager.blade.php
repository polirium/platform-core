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
            <h3 class="card-title">{{ __('Quản lý Module') }}</h3>
            <div class="card-actions">
                <button class="btn btn-primary" wire:click="discover" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="discover">
                        <i class="ti ti-refresh me-1"></i> {{ __('Quét Module') }}
                    </span>
                    <span wire:loading wire:target="discover">
                        <span class="spinner-border spinner-border-sm me-1"></span> {{ __('Đang quét...') }}
                    </span>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($modules->isEmpty())
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-package-off" style="font-size: 3rem;"></i>
                    </div>
                    <p class="empty-title">{{ __('Chưa có module nào') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('Thêm module vào thư mục platform/modules và nhấn "Quét Module" để phát hiện.') }}
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>{{ __('Module') }}</th>
                                <th>{{ __('Phiên bản') }}</th>
                                <th>{{ __('Trạng thái') }}</th>
                                <th class="w-1">{{ __('Thao tác') }}</th>
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
                                                <button class="btn btn-sm btn-success"
                                                        wire:click="install('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        title="Cài đặt module">
                                                    {{ tabler_icon('download') }}
                                                    {{ __('Cài đặt') }}
                                                </button>
                                            @elseif($module->status === 'installed')
                                                <button class="btn btn-sm btn-primary"
                                                        wire:click="enable('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        title="Kích hoạt module">
                                                    {{ tabler_icon('player-play') }}
                                                    {{ __('Kích hoạt') }}
                                                </button>
                                            @elseif($module->status === 'active')
                                                <button class="btn btn-sm btn-warning"
                                                        wire:click="disable('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        title="Tạm tắt module">
                                                    {{ tabler_icon('player-pause') }}
                                                    {{ __('Tắt') }}
                                                </button>
                                            @elseif($module->status === 'disabled')
                                                <button class="btn btn-sm btn-primary"
                                                        wire:click="enable('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        title="Bật lại module">
                                                    {{ tabler_icon('player-play') }}
                                                    {{ __('Bật lại') }}
                                                </button>
                                            @endif

                                            @if($module->status !== 'pending')
                                                <button class="btn btn-sm btn-outline-danger"
                                                        wire:click="uninstall('{{ $module->name }}')"
                                                        wire:loading.attr="disabled"
                                                        wire:confirm="Bạn có chắc muốn gỡ cài đặt module này? Thao tác này có thể xóa dữ liệu của module."
                                                        title="Gỡ cài đặt module">
                                                    {{ tabler_icon('trash') }}
                                                    {{ __('Gỡ') }}
                                                </button>
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
                        <label class="form-label">{{ __('Tên thư mục') }}</label>
                        <div class="form-control-plaintext">{{ $selectedModule->name }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Phiên bản') }}</label>
                        <div class="form-control-plaintext">{{ $selectedModule->version ?? '1.0.0' }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Mô tả') }}</label>
                        <div class="form-control-plaintext">{{ $selectedModule->description ?? __('Không có mô tả') }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Namespace') }}</label>
                        <div class="form-control-plaintext"><code>{{ $selectedModule->namespace }}</code></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Provider') }}</label>
                        <div class="form-control-plaintext"><code>{{ $selectedModule->provider }}</code></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Đường dẫn') }}</label>
                        <div class="form-control-plaintext"><code>{{ $selectedModule->path }}</code></div>
                    </div>
                </div>
            </div>
        </x-ui::modal>
    @endif
</div>
