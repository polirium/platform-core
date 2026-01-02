<x-ui.layouts::app>
    <x-slot:title>{{ __('Quản lý vai trò') }}</x-slot:title>

    @php
        $totalRoles = \Polirium\Core\Base\Http\Models\Role::count();
        $totalPermissions = \Spatie\Permission\Models\Permission::count();
    @endphp

    <!-- Stats Cards -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Tổng vai trò') }}</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($totalRoles) }}</div>
                    <div class="text-muted">{{ __('Vai trò trong hệ thống') }}</div>
                </div>
                <div class="card-footer bg-transparent">
                    <span class="avatar bg-blue-lt rounded">
                        <i class="ti ti-users-group"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Tổng quyền') }}</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($totalPermissions) }}</div>
                    <div class="text-muted">{{ __('Quyền đã được định nghĩa') }}</div>
                </div>
                <div class="card-footer bg-transparent">
                    <span class="avatar bg-green-lt rounded">
                        <i class="ti ti-lock"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-4">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <div class="subheader">{{ __('Hành động nhanh') }}</div>
                        <div class="h3 mb-3">{{ __('Tạo vai trò mới') }}</div>
                        <button type="button" class="btn btn-primary" onclick="Livewire.dispatch('show-modal-create-role');">
                            <i class="ti ti-plus me-1"></i>
                            {{ __('Thêm vai trò') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Table Card -->
    <div class="row row-deck row-cards">
        <x-ui::card>
            <x-slot name="header">
                <div class="d-flex align-items-center">
                    <i class="ti ti-users-group me-2"></i>
                    {{ __('Danh sách vai trò') }}
                </div>
            </x-slot>

            @livewire('core/base::role-table')
            @livewire('core/base::roles.modal.modal-create-role')
        </x-ui::card>
    </div>
</x-ui.layouts::app>
