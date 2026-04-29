<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-4">
            @foreach($stats as $stat)
                <div class="d-flex align-items-center">
                    <span class="avatar bg-{{ $stat['color'] }}-lt me-3">
                        @if($stat['icon'] === 'users')
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-{{ $stat['color'] }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                        @elseif($stat['icon'] === 'user-check')
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-{{ $stat['color'] }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4"/><path d="M15 19l2 2l4 -4"/></svg>
                        @elseif($stat['icon'] === 'shield')
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-{{ $stat['color'] }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-{{ $stat['color'] }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1z"/><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1z"/><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1z"/></svg>
                        @endif
                    </span>
                    <div>
                        <div class="h3 mb-0">{{ $stat['value'] }}</div>
                        <div class="text-muted small">{{ $stat['label'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
