<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            {{-- Avatar --}}
            @php
                $avatarUrl = null;
                if ($user && $user->avatar) {
                    // Check multiple possible paths
                    if (filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                        $avatarUrl = $user->avatar; // Full URL
                    } elseif (file_exists(public_path('storage/' . $user->avatar))) {
                        $avatarUrl = asset('storage/' . $user->avatar);
                    } elseif (file_exists(storage_path('app/public/' . $user->avatar))) {
                        $avatarUrl = asset('storage/' . $user->avatar);
                    }
                }
                $initials = strtoupper(substr($user->name ?? 'A', 0, 1)) . strtoupper(substr(explode(' ', $user->name ?? 'Admin')[1] ?? '', 0, 1));
            @endphp
            <span class="avatar avatar-lg bg-primary text-white me-3" style="font-size: 1.25rem; font-weight: 600;">
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="Avatar" style="object-fit: cover;">
                @else
                    {{ $initials ?: 'U' }}
                @endif
            </span>
            <div>
                <h3 class="mb-0">{{ __('core/base::general.hello') }}, {{ $user->name ?? 'Admin' }}!</h3>
                <div class="text-muted">{{ __('core/base::general.welcome_message') }}</div>
            </div>
        </div>

        @if(!empty($quickActions))
            <div class="hr-text">{{ __('core/base::general.quick_actions') }}</div>
            <div class="d-flex gap-2 flex-wrap">
                @foreach($quickActions as $action)
                    <a href="{{ route($action['route']) }}" class="btn btn-{{ $action['color'] ?? 'primary' }}">
                        {!! tabler_icon($action['icon'], ['class' => 'icon']) !!}
                        {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
