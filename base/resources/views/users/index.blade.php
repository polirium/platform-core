<x-ui.layouts::app>
    <x-slot:title>{{ __('Quản lý người dùng') }}</x-slot:title>

    @php
        $totalUsers = \Polirium\Core\Base\Http\Models\User::count();
        $activeUsers = \Polirium\Core\Base\Http\Models\User::where('status', 'active')->count();
        $superAdmins = \Polirium\Core\Base\Http\Models\User::where('super_admin', true)->count();
        $newThisMonth = \Polirium\Core\Base\Http\Models\User::whereMonth('created_at', now()->month)->count();
    @endphp

    <!-- Stats Cards -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Tổng người dùng') }}</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($totalUsers) }}</div>
                    <div class="d-flex mb-2">
                        <div>
                            <span class="text-muted">{{ __('Tất cả tài khoản trong hệ thống') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent mt-auto">
                    <span class="avatar bg-blue-lt rounded">
                        <i class="ti ti-users"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Đang hoạt động') }}</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($activeUsers) }}</div>
                    <div class="d-flex mb-2">
                        <div>
                            <span class="text-success">{{ $totalUsers > 0 ? round($activeUsers / $totalUsers * 100) : 0 }}%</span>
                            <span class="text-muted">{{ __('người dùng active') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent mt-auto">
                    <span class="avatar bg-green-lt rounded">
                        <i class="ti ti-user-check"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Super Admin') }}</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($superAdmins) }}</div>
                    <div class="d-flex mb-2">
                        <div>
                            <span class="text-muted">{{ __('Có quyền cao nhất') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent mt-auto">
                    <span class="avatar bg-purple-lt rounded">
                        <i class="ti ti-shield-check"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Mới tháng này') }}</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($newThisMonth) }}</div>
                    <div class="d-flex mb-2">
                        <div>
                            <span class="text-muted">{{ __('Tạo trong tháng') }} {{ now()->format('m/Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent mt-auto">
                    <span class="avatar bg-orange-lt rounded">
                        <i class="ti ti-user-plus"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- User Table Card -->
    <div class="row row-deck row-cards">
        <x-ui::card>
            <x-slot name="header">
                <div class="d-flex align-items-center">
                    <i class="ti ti-users me-2"></i>
                    {{ __('Danh sách người dùng') }}
                </div>
            </x-slot>

            <x-slot name="action">
                <button type="button" class="btn btn-primary" onclick="Livewire.dispatch('show-modal-create-user');">
                    <i class="ti ti-plus me-1"></i>
                    {{ __('Thêm người dùng') }}
                </button>
            </x-slot>

            @livewire('core/base::user-table')

            @livewire('core/base::user.modal')
            @livewire('core/base::user.modal.delete')
        </x-ui::card>
    </div>
</x-ui.layouts::app>
