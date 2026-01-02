<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-user" header="{{ $modalTitle }}" class="modal-xl">

            <div class="row g-4">
                <!-- Left Column: Avatar -->
                <div class="col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <!-- Clickable Avatar Upload -->
                            <div class="position-relative d-inline-block mb-3" style="cursor: pointer;" onclick="document.getElementById('avatar-input').click()">
                                <div class="avatar avatar-xxl rounded-circle" style="width: 150px; height: 150px;">
                                    @if ($avatar_file)
                                        <img src="{{ $avatar_file->temporaryUrl() }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    @elseif ($isEdit && !empty($user['avatar']) && str_starts_with($user['avatar'], 'avatars/'))
                                        <img src="{{ asset('storage/' . $user['avatar']) }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <span class="avatar avatar-xxl rounded-circle bg-primary-lt" style="width: 150px; height: 150px;">
                                            <i class="ti ti-user" style="font-size: 4rem;"></i>
                                        </span>
                                    @endif
                                </div>
                                <!-- Upload overlay -->
                                <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2" style="transform: translate(25%, 25%);">
                                    <i class="ti ti-camera" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>
                            <!-- Hidden file input -->
                            <input type="file" id="avatar-input" class="d-none" wire:model="avatar_file" accept="image/*">

                            <div class="text-muted small">{{ __('Bấm để đổi ảnh') }}</div>
                            <div class="text-muted small">{{ __('Tối đa 2MB') }}</div>

                            @error('avatar_file')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror

                            <div wire:loading wire:target="avatar_file" class="text-primary small mt-2">
                                <i class="ti ti-loader-2 icon-spin"></i> {{ __('Đang tải...') }}
                            </div>

                            <!-- Super Admin Toggle -->
                            <hr class="my-3">
                            <label class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" wire:model="user.super_admin">
                                <span class="form-check-label">
                                    <i class="ti ti-shield-check me-1"></i>
                                    {{ __('Super Admin') }}
                                </span>
                            </label>
                            <div class="text-muted small">{{ __('Quyền cao nhất') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form Fields -->
                <div class="col-lg-9">
                    <div class="row g-3">
                        <!-- Row 1: Name fields -->
                        <div class="col-md-6">
                            <x-form::input wire:model="user.first_name" :label="__('Họ')" :placeholder="__('Nhập họ...')" required />
                        </div>
                        <div class="col-md-6">
                            <x-form::input wire:model="user.last_name" :label="__('Tên')" :placeholder="__('Nhập tên...')" required />
                        </div>

                        <!-- Row 2: Username & Email -->
                        <div class="col-md-6">
                            <x-form::input wire:model="user.username" :label="__('Username')" :placeholder="__('Nhập username...')" required />
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative">
                                <x-form::input wire:model="user.email" :label="__('Email')" :placeholder="__('email@example.com')" type="email" required />
                                @if ($isEdit && isset($user['email_verified_at']) && $user['email_verified_at'])
                                    <span class="position-absolute badge bg-success" style="top: 0; right: 0;">
                                        <i class="ti ti-check"></i> {{ __('Đã xác thực') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Row 3: Phone & Status -->
                        <div class="col-md-6">
                            <x-form::input wire:model="user.phone" :label="__('Số điện thoại')" :placeholder="__('0912 345 678')" type="tel" />
                        </div>
                        <div class="col-md-6">
                            <x-form::select wire:model="user.status" :label="__('Trạng thái')" :placeholder="__('Chọn trạng thái...')" :options="$list['statuses']" required />
                        </div>

                        <!-- Row 4: Password -->
                        @if (!$isEdit)
                            <div class="col-md-6">
                                <x-form::input wire:model="user.password" :label="__('Mật khẩu')" :placeholder="__('Nhập mật khẩu...')" type="password" required />
                            </div>
                            <div class="col-md-6">
                                <x-form::input wire:model="user.password_confirmation" :label="__('Xác nhận mật khẩu')" :placeholder="__('Nhập lại mật khẩu...')" type="password" required />
                            </div>
                        @else
                            <div class="col-md-6">
                                <x-form::input wire:model="user.password" :label="__('Mật khẩu mới')" :placeholder="__('Nhập mật khẩu mới...')" type="password" />
                                <div class="form-hint">{{ __('Để trống nếu giữ nguyên') }}</div>
                            </div>
                            <div class="col-md-6">
                                <x-form::input wire:model="user.password_confirmation" :label="__('Xác nhận mật khẩu')" :placeholder="__('Nhập lại mật khẩu...')" type="password" />
                            </div>
                        @endif

                        <!-- Row 5: Roles & Branches -->
                        <div class="col-md-6">
                            <x-form::select wire:model.live="role_ids" :label="__('Vai trò')" :placeholder="__('Chọn vai trò...')" :options="$list['roles']" tomselect multiple />
                        </div>
                        <div class="col-md-6">
                            <x-form::select wire:model="branch_ids" :label="__('Chi nhánh')" :options="$list['branches']" tomselect multiple />
                        </div>

                        <!-- Row 6: Direct Permissions -->
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header py-2">
                                    <h4 class="card-title mb-0">
                                        <i class="ti ti-lock me-2"></i>
                                        {{ __('Quyền bổ sung') }}
                                        @if(count($permission_ids) > 0)
                                            <span class="badge text-bg-primary ms-2">{{ count($permission_ids) }}</span>
                                        @endif
                                    </h4>
                                    <div class="small mt-2 ms-4 ps-2">
                                        {{ __('Cấp thêm quyền ngoài vai trò') }}
                                    </div>
                                </div>
                                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                                    @if (isset($list['permission_tree']) && isset($list['permission_flags']))
                                        <div class="row g-2">
                                            @foreach ($list['permission_tree']['root'] ?? [] as $element)
                                                <div class="col-md-4">
                                                    <div class="border rounded p-2 mb-2">
                                                        <div class="fw-bold text-primary small mb-1">
                                                            <i class="ti ti-folder me-1"></i>
                                                            {{ trans($list['permission_flags'][$element]['name'] ?? $element) }}
                                                        </div>
                                                        @if (isset($list['permission_tree'][$element]))
                                                            @foreach ($list['permission_tree'][$element] as $subElement)
                                                                @php
                                                                    $permFlag = $list['permission_flags'][$subElement]['flag'] ?? $subElement;
                                                                    $isFromRole = in_array($permFlag, $rolePermissions ?? []);
                                                                @endphp
                                                                <label class="form-check mb-0 {{ $isFromRole ? 'opacity-50' : '' }}">
                                                                    <input
                                                                        class="form-check-input"
                                                                        type="checkbox"
                                                                        value="{{ $permFlag }}"
                                                                        wire:model.live="permission_ids"
                                                                        {{ $isFromRole ? 'disabled checked' : '' }}
                                                                    >
                                                                    <span class="form-check-label small">
                                                                        {{ trans($list['permission_flags'][$subElement]['name'] ?? $subElement) }}
                                                                        @if($isFromRole)
                                                                            <span class="badge text-bg-secondary ms-1" style="font-size: 0.65rem;">Từ vai trò</span>
                                                                        @endif
                                                                    </span>
                                                                </label>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted text-center py-3">
                                            {{ __('Không có quyền nào để hiển thị') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">
                    {{ __('Hủy') }}
                </button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <i class="ti ti-device-floppy me-1"></i>
                        {{ $isEdit ? __('Cập nhật') : __('Tạo mới') }}
                    </span>
                    <span wire:loading wire:target="save">
                        <i class="ti ti-loader-2 icon-spin me-1"></i>
                        {{ __('Đang lưu...') }}
                    </span>
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
