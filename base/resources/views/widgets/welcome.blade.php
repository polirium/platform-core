<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            {{-- Avatar with initials --}}
            <span class="avatar avatar-lg bg-primary text-white me-3" style="font-size: 1.25rem; font-weight: 600;">
                @if($user && $user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="object-fit: cover;">
                @else
                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name ?? 'Admin')[1] ?? '', 0, 1)) }}
                @endif
            </span>
            <div>
                <h3 class="mb-0">{{ __('Xin chào') }}, {{ $user->name ?? 'Admin' }}!</h3>
                <div class="text-muted">{{ __('Chúc bạn một ngày làm việc hiệu quả') }}</div>
            </div>
        </div>

        @if(!empty($quickActions))
            <div class="hr-text">{{ __('Hành động nhanh') }}</div>
            <div class="d-flex gap-2 flex-wrap">
                @foreach($quickActions as $action)
                    <a href="{{ route($action['route']) }}" class="btn btn-{{ $action['color'] ?? 'primary' }}">
                        @if($action['icon'] === 'user-plus')
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /></svg>
                        @elseif($action['icon'] === 'shield')
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /></svg>
                        @endif
                        {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
