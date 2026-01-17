<div>
    <x-ui::modal id="modal-detail-user" class="modal-xl">
        <x-slot:header>
            <div class="d-flex align-items-center gap-3">
                <div class="modal-icon bg-primary-lt">
                    {!! tabler_icon('user', ['class' => 'ti']) !!}
                </div>
                <div>
                    <h4 class="modal-title mb-0">{{ __('core/base::general.user_list') }}</h4>
                    @if(isset($user))
                        <p class="text-muted small mb-0 mt-1">
                            {{ __('core/base::general.id') }}: #{{ $user['id'] ?? '—' }}
                        </p>
                    @endif
                </div>
            </div>
        </x-slot:header>

        @if(isset($user))
            <div class="user-detail-wrapper">
                <!-- Profile Header Card -->
                <div class="user-profile-header mb-4">
                    <div class="row g-4">
                        <!-- Avatar Section -->
                        <div class="col-auto">
                            <div class="user-avatar-wrapper">
                                @if (!empty($user['avatar']))
                                    @if (str_starts_with($user['avatar'], 'http://') || str_starts_with($user['avatar'], 'https://'))
                                        <img
                                            src="{{ $user['avatar'] }}"
                                            alt="{{ $user['username'] }}"
                                            class="user-avatar"
                                        >
                                    @elseif (str_starts_with($user['avatar'], 'avatars/') || str_starts_with($user['avatar'], '/'))
                                        <img
                                            src="{{ asset('storage/' . ltrim($user['avatar'], '/')) }}"
                                            alt="{{ $user['username'] }}"
                                            class="user-avatar"
                                        >
                                    @else
                                        <img
                                            src="{{ asset('storage/avatars/' . $user['avatar']) }}"
                                            alt="{{ $user['username'] }}"
                                            class="user-avatar"
                                        >
                                    @endif
                                @else
                                    <div class="user-avatar-placeholder">
                                        {{ mb_strtoupper(mb_substr($user['first_name'] ?? $user['username'] ?? '?', 0, 1)) }}
                                    </div>
                                @endif

                                <!-- Status Indicator -->
                                <div class="user-status-indicator {{ ($user['status'] ?? 'active') === 'active' ? 'status-active' : 'status-inactive' }}"></div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="col">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="user-name mb-1">
                                        {{ $user['first_name'] ?? '' }} {{ $user['last_name'] ?? '' }}
                                    </h3>
                                    <p class="user-username mb-2">@{{ $user['username'] ?? '' }}</p>

                                    <!-- Badges -->
                                    <div class="d-flex flex-wrap gap-2">
                                        @if($user['super_admin'] ?? false)
                                            <span class="badge badge-primary badge-sm">
                                                {!! tabler_icon('shield', ['class' => 'ti icon-xs']) !!}
                                                {{ __('core/base::general.super_admin') }}
                                            </span>
                                        @endif

                                        <span class="badge {{ ($user['status'] ?? 'active') === 'active' ? 'bg-success-lt text-success' : 'bg-danger-lt text-danger' }} badge-sm">
                                            {{ ($user['status'] ?? 'active') === 'active' ? __('core/base::general.active') : __('core/base::general.inactive') }}
                                        </span>

                                        @if(isset($user['email_verified_at']) && $user['email_verified_at'])
                                            <span class="badge bg-success-lt text-success badge-sm">
                                                {!! tabler_icon('check', ['class' => 'ti icon-xs']) !!}
                                                {{ __('core/base::general.verified') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="btn-list flex-nowrap">
                                    @if(auth()->user()->can('users.edit'))
                                        <x-ui::button
                                            color="primary"
                                            size="sm"
                                            icon="edit"
                                            :outline="true"
                                            onclick="Livewire.dispatch('close-modal-detail-user'); Livewire.dispatch('show-modal-edit-user', {id: {{ $user['id'] ?? 0 }}})"
                                        >
                                            {{ __('core/base::general.edit') }}
                                        </x-ui::button>
                                    @endif

                                    @if(auth()->user()->can('users.delete'))
                                        <x-ui::button
                                            color="danger"
                                            size="sm"
                                            icon="trash"
                                            :outline="true"
                                            wire:click="deleteUser({{ $user['id'] ?? 0 }})"
                                            wire:confirm="{{ __('core/base::general.confirm_delete') }}"
                                        >
                                            {{ __('core/base::general.delete') }}
                                        </x-ui::button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Sections -->
                <div class="row g-4">
                    <!-- Contact Information -->
                    <div class="col-md-6">
                        <x-ui::card class="h-100">
                            <div class="card-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="card-icon bg-info-lt text-info">
                                        {!! tabler_icon('mail', ['class' => 'ti']) !!}
                                    </div>
                                    <h5 class="card-title mb-0">{{ __('Thông tin liên hệ') }}</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-list">
                                    <!-- Email -->
                                    <div class="info-item">
                                        <div class="info-label">
                                            {!! tabler_icon('mail', ['class' => 'ti icon text-muted']) !!}
                                            {{ __('core/base::general.email') }}
                                        </div>
                                        <div class="info-value">
                                            {{ $user['email'] ?? '—' }}
                                            @if(isset($user['email_verified_at']) && $user['email_verified_at'])
                                                <i class="ti ti-check text-success ms-1" title="{{ __('core/base::general.verified') }}"></i>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="info-item">
                                        <div class="info-label">
                                            {!! tabler_icon('phone', ['class' => 'ti icon text-muted']) !!}
                                            {{ __('core/base::general.phone_number') }}
                                        </div>
                                        <div class="info-value">{{ $user['phone'] ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        </x-ui::card>
                    </div>

                    <!-- Account Information -->
                    <div class="col-md-6">
                        <x-ui::card class="h-100">
                            <div class="card-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="card-icon bg-warning-lt text-warning">
                                        {!! tabler_icon('shield', ['class' => 'ti']) !!}
                                    </div>
                                    <h5 class="card-title mb-0">{{ __('Thông tin tài khoản') }}</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-list">
                                    <!-- Username -->
                                    <div class="info-item">
                                        <div class="info-label">
                                            {!! tabler_icon('user', ['class' => 'ti icon text-muted']) !!}
                                            {{ __('core/base::general.username') }}
                                        </div>
                                        <div class="info-value">
                                            <code class="code-sm">{{ $user['username'] ?? '—' }}</code>
                                        </div>
                                    </div>

                                    <!-- Roles -->
                                    <div class="info-item">
                                        <div class="info-label">
                                            {!! tabler_icon('badge', ['class' => 'ti icon text-muted']) !!}
                                            {{ __('core/base::general.roles') }}
                                        </div>
                                        <div class="info-value">
                                            @if(isset($roles) && $roles->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($roles as $role)
                                                        <span class="badge badge-primary">{{ $role->name }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Branches -->
                                    <div class="info-item">
                                        <div class="info-label">
                                            {!! tabler_icon('building-arch', ['class' => 'ti icon text-muted']) !!}
                                            {{ __('core/base::general.branch') }}
                                        </div>
                                        <div class="info-value">
                                            @if(isset($branches) && $branches->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($branches as $branch)
                                                        <span class="badge badge-info">{{ $branch->name }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-ui::card>
                    </div>

                    <!-- Timestamp Information -->
                    <div class="col-12">
                        <x-ui::card>
                            <div class="card-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="card-icon bg-success-lt text-success">
                                        {!! tabler_icon('clock', ['class' => 'ti']) !!}
                                    </div>
                                    <h5 class="card-title mb-0">{{ __('Thông tin thời gian') }}</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="info-item-inline">
                                            <div class="info-label-inline text-muted small">{{ __('core/base::general.created_at') }}</div>
                                            <div class="info-value-inline fw-semibold">{{ CoreSupport::datetime($user['created_at'] ?? now()) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-item-inline">
                                            <div class="info-label-inline text-muted small">{{ __('core/base::general.updated_at') }}</div>
                                            <div class="info-value-inline fw-semibold">{{ CoreSupport::datetime($user['updated_at'] ?? now()) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-ui::card>
                    </div>
                </div>
            </div>
        @endif

        <x-slot:footer>
            <x-ui::button color="secondary" data-bs-dismiss="modal">
                {{ __('core/base::general.close') }}
            </x-ui::button>
        </x-slot:footer>
    </x-ui::modal>

    <style>
        /* User Detail Modal Styles */
        .user-detail-wrapper {
            /* Container styles */
        }

        /* Profile Header */
        .user-profile-header {
            background: linear-gradient(135deg, var(--tblr-body-bg) 0%, var(--tblr-muted-bg) 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid var(--tblr-border-color);
        }

        /* Avatar */
        .user-avatar-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--tblr-body-bg);
            box-shadow: 0 0 0 2px var(--tblr-primary);
        }

        .user-avatar-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-primary-bg-subtle) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            border: 3px solid var(--tblr-body-bg);
            box-shadow: 0 0 0 2px var(--tblr-primary);
        }

        .user-status-indicator {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 3px solid var(--tblr-body-bg);
        }

        .user-status-indicator.status-active {
            background: var(--tblr-success);
        }

        .user-status-indicator.status-inactive {
            background: var(--tblr-danger);
        }

        /* User Name & Username */
        .user-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--tblr-heading-color);
        }

        .user-username {
            color: var(--tblr-muted);
            font-size: 0.95rem;
        }

        /* Info List */
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--tblr-border-color-translucent);
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: var(--tblr-muted);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-label .icon {
            font-size: 1rem;
        }

        .info-value {
            font-weight: 500;
            color: var(--tblr-body-color);
        }

        /* Card Icons */
        .card-icon {
            width: 36px;
            height: 36px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Badge adjustments */
        .badge-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-primary {
            background: var(--tblr-primary);
            color: white;
        }

        .badge-info {
            background: var(--tblr-info);
            color: white;
        }

        /* Code style */
        .code-sm {
            font-size: 0.85rem;
            padding: 0.125rem 0.375rem;
        }

        /* Inline info items */
        .info-item-inline {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
    </style>
</div>
