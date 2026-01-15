<div>
    <x-ui::modal id="modal-detail-user" header="{{ __('core/base::general.user_list') }}" class="modal-lg">
        @if(isset($user))
            <div class="row g-4">
                <!-- Left Column: Avatar & Basic Info -->
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <!-- Avatar -->
                            <div class="mb-3">
                                @if (!empty($user['avatar']))
                                    @if (str_starts_with($user['avatar'], 'http://') || str_starts_with($user['avatar'], 'https://'))
                                        <img
                                            src="{{ $user['avatar'] }}"
                                            alt="{{ $user['username'] }}"
                                            class="rounded-circle"
                                            style="width: 120px; height: 120px; object-fit: cover;"
                                        >
                                    @elseif (str_starts_with($user['avatar'], 'avatars/') || str_starts_with($user['avatar'], '/'))
                                        <img
                                            src="{{ asset('storage/' . ltrim($user['avatar'], '/')) }}"
                                            alt="{{ $user['username'] }}"
                                            class="rounded-circle"
                                            style="width: 120px; height: 120px; object-fit: cover;"
                                        >
                                    @else
                                        <img
                                            src="{{ asset('storage/avatars/' . $user['avatar']) }}"
                                            alt="{{ $user['username'] }}"
                                            class="rounded-circle"
                                            style="width: 120px; height: 120px; object-fit: cover;"
                                        >
                                    @endif
                                @else
                                    <span class="avatar avatar-xl rounded-circle bg-primary-lt" style="width: 120px; height: 120px; display: inline-flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 600;">
                                        {{ mb_strtoupper(mb_substr($user['first_name'] ?? $user['username'] ?? '?', 0, 1)) }}
                                    </span>
                                @endif
                            </div>

                            <h4 class="mb-1">{{ $user['first_name'] ?? '' }} {{ $user['last_name'] ?? '' }}</h4>
                            <p class="text-muted mb-3">@{{ $user['username'] ?? '' }}</p>

                            <hr class="my-3">

                            <!-- Status Badges -->
                            <div class="d-flex flex-column gap-2 text-start">
                                @if($user['super_admin'] ?? false)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge text-bg-primary">
                                            {!! tabler_icon('shield-check', ['class' => 'ti me-1']) !!}
                                            {{ __('core/base::general.super_admin') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">{{ __('core/base::general.status') }}:</span>
                                    <span class="badge {{ ($user['status'] ?? 'active') === 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ($user['status'] ?? 'active') === 'active' ? __('Hoạt động') : __('Không hoạt động') }}
                                    </span>
                                </div>

                                @if(isset($user['email_verified_at']) && $user['email_verified_at'])
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success">
                                            {!! tabler_icon('check', ['class' => 'ti me-1']) !!}
                                            {{ __('core/base::general.verified') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Details -->
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                {!! tabler_icon('user', ['class' => 'ti me-2']) !!}
                                {{ __('core/base::general.personal_information') }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Username -->
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small">{{ __('core/base::general.username') }}</label>
                                    <div class="fw-semibold">{{ $user['username'] ?? '—' }}</div>
                                </div>

                                <!-- Email -->
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small">{{ __('core/base::general.email') }}</label>
                                    <div class="fw-semibold">
                                        {{ $user['email'] ?? '—' }}
                                        @if(isset($user['email_verified_at']) && $user['email_verified_at'])
                                            <i class="ti ti-check text-success ms-1" title="{{ __('core/base::general.verified') }}"></i>
                                        @endif
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small">{{ __('core/base::general.phone_number') }}</label>
                                    <div class="fw-semibold">{{ $user['phone'] ?? '—' }}</div>
                                </div>

                                <!-- Status -->
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small">{{ __('core/base::general.status') }}</label>
                                    <div class="fw-semibold">
                                        <span class="badge {{ ($user['status'] ?? 'active') === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ($user['status'] ?? 'active') === 'active' ? __('Hoạt động') : __('Không hoạt động') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Roles -->
                                <div class="col-12">
                                    <label class="form-label text-muted small">{{ __('core/base::general.roles') }}</label>
                                    <div>
                                        @if(isset($roles) && $roles->isNotEmpty())
                                            @foreach($roles as $role)
                                                <span class="badge text-bg-primary me-1">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Branches -->
                                <div class="col-12">
                                    <label class="form-label text-muted small">{{ __('core/base::general.branch') }}</label>
                                    <div>
                                        @if(isset($branches) && $branches->isNotEmpty())
                                            @foreach($branches as $branch)
                                                <span class="badge text-bg-info me-1">{{ $branch->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Created At -->
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small">{{ __('core/base::general.created_at') }}</label>
                                    <div class="fw-semibold">{{ CoreSupport::datetime($user['created_at'] ?? now()) }}</div>
                                </div>

                                <!-- Updated At -->
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small">{{ __('core/base::general.updated_at') }}</label>
                                    <div class="fw-semibold">{{ CoreSupport::datetime($user['updated_at'] ?? now()) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <x-slot name="footer">
            <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">
                {{ __('core/base::general.cancel') }}
            </button>
            @if(auth()->user()->can('users.edit'))
                <button type="button" class="btn btn-primary" onclick="Livewire.dispatch('show-modal-edit-user', {id: {{ $user['id'] ?? 0 }}});">
                    {!! tabler_icon('edit', ['class' => 'ti me-1']) !!}
                    {{ __('core/base::general.edit') }}
                </button>
            @endif
        </x-slot>
    </x-ui::modal>
</div>
