<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-user" header="{{ $modalTitle }}" class="modal-xl">

            <div class="row g-4">
                <!-- Left Column: Avatar -->
                <div class="col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <!-- Avatar Upload -->
                            <div class="avatar-upload-wrapper" x-data="{
                                hasPreview: {{ $avatar_file || ($user['avatar'] ?? null) ? 'true' : 'false' }}
                            }">
                                <div class="avatar-upload-container d-inline-block position-relative mb-3 text-center">
                                    <div
                                        class="avatar avatar-xxl rounded-circle"
                                        style="width: 150px; height: 150px; overflow: hidden; cursor: pointer;"
                                        @click="$refs.avatarInput.click()"
                                    >
                                        @if ($avatar_file)
                                            <img
                                                src="{{ $avatar_file->temporaryUrl() }}"
                                                alt="Avatar preview"
                                                class="rounded-circle"
                                                style="width: 150px; height: 150px; object-fit: cover;"
                                            >
                                        @elseif (!empty($user['avatar']))
                                            @if (str_starts_with($user['avatar'], 'http://') || str_starts_with($user['avatar'], 'https://'))
                                                <img
                                                    src="{{ $user['avatar'] }}"
                                                    alt="Current avatar"
                                                    class="rounded-circle"
                                                    style="width: 150px; height: 150px; object-fit: cover;"
                                                >
                                            @elseif (str_starts_with($user['avatar'], 'avatars/') || str_starts_with($user['avatar'], '/'))
                                                <img
                                                    src="{{ asset('storage/' . ltrim($user['avatar'], '/')) }}"
                                                    alt="Current avatar"
                                                    class="rounded-circle"
                                                    style="width: 150px; height: 150px; object-fit: cover;"
                                                >
                                            @else
                                                <img
                                                    src="{{ asset('storage/avatars/' . $user['avatar']) }}"
                                                    alt="Current avatar"
                                                    class="rounded-circle"
                                                    style="width: 150px; height: 150px; object-fit: cover;"
                                                >
                                            @endif
                                        @else
                                            <span class="avatar avatar-xxl rounded-circle bg-primary-lt" style="width: 150px; height: 150px;">
                                                {!! tabler_icon('user', ['class' => 'ti', 'style' => 'font-size: 4rem;']) !!}
                                            </span>
                                        @endif
                                    </div>
                                    <div
                                        class="avatar-upload-badge position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px; transform: translate(25%, 25%); cursor: pointer;"
                                        @click="$refs.avatarInput.click()"
                                    >
                                        {!! tabler_icon('camera', ['class' => 'ti', 'style' => 'font-size: 1.2rem;']) !!}
                                    </div>
                                </div>

                                <!-- Hidden file input -->
                                <input
                                    type="file"
                                    x-ref="avatarInput"
                                    class="d-none"
                                    wire:model="avatar_file"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                >

                                <!-- Helper text -->
                                <div class="text-muted small">
                                    <span
                                        class="avatar-upload-click-hint"
                                        style="cursor: pointer; text-decoration: underline;"
                                        @click="$refs.avatarInput.click()"
                                    >
                                        {{ __('core/base::general.click_to_change_image') }}
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    JPG, PNG, GIF, WebP (max 2MB)
                                </div>

                                <!-- Error message -->
                                @error('avatar_file')
                                    <div class="text-danger small mt-2">
                                        <i class="ti ti-alert-circle"></i> {{ $message }}
                                    </div>
                                @enderror

                                <!-- Loading state -->
                                <div wire:loading wire:target="avatar_file" class="text-primary small mt-2">
                                    <i class="ti ti-loader-2 icon-spin"></i> {{ __('core/base::general.loading') }}
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Super Admin Toggle -->
                            <label class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" wire:model="user.super_admin">
                                <span class="form-check-label">
                                    {!! tabler_icon('shield-check', ['class' => 'ti me-1']) !!}
                                    {{ __('core/base::general.super_admin') }}
                                </span>
                            </label>
                            <div class="text-muted small">{{ __('core/base::general.is_admin') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form Fields -->
                <div class="col-lg-9">
                    <div class="row g-3">
                        <!-- Row 1: Name fields -->
                        <div class="col-md-6">
                            <x-form::input wire:model="user.first_name" :label="__('core/base::general.first_name')" :placeholder="__('core/base::general.enter_first_name')" required />
                        </div>
                        <div class="col-md-6">
                            <x-form::input wire:model="user.last_name" :label="__('core/base::general.last_name')" :placeholder="__('core/base::general.enter_last_name')" required />
                        </div>

                        <!-- Row 2: Username & Email -->
                        <div class="col-md-6">
                            <x-form::input wire:model="user.username" :label="__('core/base::general.username')" :placeholder="__('core/base::general.enter_username')" required />
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative">
                                <x-form::input wire:model="user.email" :label="__('core/base::general.email')" :placeholder="__('core/base::general.enter_email')" type="email" required />
                                @if ($isEdit && isset($user['email_verified_at']) && $user['email_verified_at'])
                                    <span class="position-absolute badge bg-success" style="top: 0; right: 0;">
                                        {!! tabler_icon('check', ['class' => 'ti']) !!} {{ __('core/base::general.verified') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Row 3: Phone & Status -->
                        <div class="col-md-6">
                            <x-form::input wire:model="user.phone" :label="__('core/base::general.phone_number')" :placeholder="__('core/base::general.phone_placeholder')" type="tel" />
                        </div>
                        <div class="col-md-6">
                            <x-form::select wire:model="user.status" :label="__('core/base::general.status')" :placeholder="__('core/base::general.select_status')" :options="$list['statuses']" required />
                        </div>

                        <!-- Row 4: Password -->
                        @if (!$isEdit)
                            <div class="col-md-6">
                                <x-form::input wire:model="user.password" :label="__('core/base::general.password')" :placeholder="__('core/base::general.enter_password')" type="password" required />
                            </div>
                            <div class="col-md-6">
                                <x-form::input wire:model="user.password_confirmation" :label="__('core/base::general.confirm_password')" :placeholder="__('core/base::general.enter_password_confirm')" type="password" required />
                            </div>
                        @else
                            <div class="col-md-6">
                                <x-form::input wire:model="user.password" :label="__('core/base::general.new_password')" :placeholder="__('core/base::general.enter_new_password')" type="password" />
                                <div class="form-hint">{{ __('core/base::general.leave_blank_to_keep') }}</div>
                            </div>
                            <div class="col-md-6">
                                <x-form::input wire:model="user.password_confirmation" :label="__('core/base::general.confirm_password')" :placeholder="__('core/base::general.enter_password_confirm')" type="password" />
                            </div>
                        @endif

                        <!-- Row 5: Roles & Branches -->
                        <div class="col-md-6">
                            <x-form::select wire:model.live="role_ids" :label="__('core/base::general.roles')" :placeholder="__('core/base::general.select_role')" :options="$list['roles']" tomselect multiple />
                        </div>
                        <div class="col-md-6">
                            <x-form::select wire:model="branch_ids" :label="__('core/base::general.branch')" :options="$list['branches']" tomselect multiple />
                        </div>

                        <!-- Row 6: Direct Permissions -->
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header py-2">
                                    <h4 class="card-title mb-0">
                                        {!! tabler_icon('lock', ['class' => 'ti me-2']) !!}
                                        {{ __('core/base::general.additional_permissions_label') }}
                                        @if(count($permission_ids) > 0)
                                            <span class="badge text-bg-primary ms-2">{{ count($permission_ids) }}</span>
                                        @endif
                                    </h4>
                                    <div class="small mt-2 ms-4 ps-2">
                                        {{ __('core/base::general.additional_permissions') }}
                                    </div>
                                </div>
                                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                                    @if (isset($list['permission_tree']) && isset($list['permission_flags']))
                                        <div class="row g-2">
                                            @foreach ($list['permission_tree']['root'] ?? [] as $element)
                                                <div class="col-md-4">
                                                    <div class="border rounded p-2 mb-2">
                                                        <div class="fw-bold text-primary small mb-1">
                                                            {!! tabler_icon('folder', ['class' => 'ti me-1']) !!}
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
                                                                        wire:model="permission_ids"
                                                                        {{ $isFromRole ? 'disabled checked' : '' }}
                                                                    >
                                                                    <span class="form-check-label small">
                                                                        {{ trans($list['permission_flags'][$subElement]['name'] ?? $subElement) }}
                                                                        @if($isFromRole)
                                                                            <span class="badge text-bg-secondary ms-1" style="font-size: 0.65rem;">{{ __('core/base::general.from_role') }}</span>
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
                                            {{ __('core/base::general.no_permissions_to_display') }}
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
                    {{ __('core/base::general.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        {!! tabler_icon('device-floppy', ['class' => 'ti me-1']) !!}
                        {{ $isEdit ? __('core/base::general.update') : __('core/base::general.create') }}
                    </span>
                    <span wire:loading wire:target="save">
                        {!! tabler_icon('loader-2', ['class' => 'ti icon-spin me-1']) !!}
                        {{ __('core/base::general.saving') }}
                    </span>
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
