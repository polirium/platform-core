<div>
    <form wire:submit.prevent="save">
        <x-ui::modal id="modal-user" header="{{ $modalTitle }}" class="modal-xl">

            <div class="row g-4">
                <!-- Profile Picture Section -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ tabler_icon('user-circle') }}
                                {{ __('Profile Picture') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar avatar-xl" style="width: 80px; height: 80px;">
                                        @if ($avatar_file)
                                            <img src="{{ $avatar_file->temporaryUrl() }}" alt="Preview" class="avatar-img rounded-circle">
                                        @elseif ($isEdit && isset($user['avatar']) && $user['avatar'])
                                            <img src="{{ asset('storage/' . $user['avatar']) }}" alt="Current Avatar" class="avatar-img rounded-circle">
                                        @else
                                            <div class="avatar-img rounded-circle bg-secondary d-flex align-items-center justify-content-center">
                                                {{ tabler_icon('user', ['class' => 'icon-lg text-white']) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('Upload New Picture') }}</label>
                                        <input type="file" class="form-control" wire:model="avatar_file" accept="image/*">
                                        <div class="form-hint">{{ __('Recommended: Square image, max 2MB') }}</div>
                                    </div>
                                    @error('avatar_file')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <div wire:loading wire:target="avatar_file" class="text-muted small">
                                        {{ tabler_icon('loader-2', ['class' => 'icon-spin']) }} {{ __('Uploading...') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ tabler_icon('user') }}
                                {{ __('Personal Information') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <x-form::input wire:model="user.first_name" :label="__('First Name')" required />
                            </div>
                            <div class="mb-3">
                                <x-form::input wire:model="user.last_name" :label="__('Last Name')" required />
                            </div>
                            <div class="mb-3">
                                <x-form::input wire:model="user.phone" :label="__('Phone Number')" type="tel" />
                            </div>
                            <div class="mb-3">
                                <x-form::select wire:model="user.status" :label="__('Account Status')" :options="$list['statuses']" required />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ tabler_icon('settings') }}
                                {{ __('Account Settings') }}
                            </h3>
                            @if ($isEdit && isset($user['email_verified_at']) && $user['email_verified_at'])
                                <div class="card-actions">
                                    <span class="badge bg-success">{{ tabler_icon('check') }} {{ __('Email Verified') }}</span>
                                </div>
                            @elseif ($isEdit)
                                <div class="card-actions">
                                    <span class="badge bg-warning">{{ tabler_icon('alert-triangle') }} {{ __('Email Not Verified') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <x-form::input wire:model="user.email" :label="__('Email Address')" type="email" required />
                            </div>
                            <div class="mb-3">
                                <x-form::input wire:model="user.username" :label="__('Username')" required />
                            </div>

                            @if (!$isEdit)
                                <div class="mb-3">
                                    <x-form::input wire:model="user.password" :label="__('Password')" type="password" required />
                                </div>
                                <div class="mb-3">
                                    <x-form::input wire:model="user.password_confirmation" :label="__('Confirm Password')" type="password" required />
                                </div>
                            @else
                                <div class="mb-3">
                                    <x-form::input wire:model="user.password" :label="__('New Password')" type="password" />
                                    <div class="form-hint">{{ __('Leave empty to keep current password') }}</div>
                                </div>
                                <div class="mb-3">
                                    <x-form::input wire:model="user.password_confirmation" :label="__('Confirm New Password')" type="password" />
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="user.super_admin">
                                    <span class="form-check-label">
                                        {{ tabler_icon('shield-check') }}
                                        {{ __('Super Administrator') }}
                                    </span>
                                </label>
                                <div class="form-hint">{{ __('Grants full system access') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions & Access -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ tabler_icon('lock') }}
                                {{ __('Permissions & Access') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <x-form::select wire:model="role_ids" :label="__('User Roles')" :options="$list['roles']" tomselect multiple />
                                        <div class="form-hint">{{ __('Select one or more roles for this user') }}</div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <x-form::select wire:model="branch_ids" :label="__('Branch Access')" :options="$list['branches']" tomselect multiple />
                                        <div class="form-hint">{{ __('Select branches this user can access') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        {{ tabler_icon('device-floppy') }}
                        {{ $isEdit ? __('Update User') : __('Create User') }}
                    </span>
                    <span wire:loading wire:target="save">
                        {{ tabler_icon('loader-2', ['class' => 'icon-spin']) }}
                        {{ __('Saving...') }}
                    </span>
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
