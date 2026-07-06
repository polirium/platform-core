<x-ui.layouts::app>
    <x-slot:title>{{ __('core/base::general.system_logs') }}</x-slot:title>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('core/base::general.system_logs') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <x-ui::card>
                <x-slot name="header">
                    <div class="d-flex align-items-center">
                        {!! tabler_icon('filter', ['class' => 'icon me-2']) !!}
                        {{ __('core/base::general.filter') }}
                    </div>
                </x-slot>

                <form method="GET" action="{{ route('core.system-logs.index') }}" class="d-grid gap-3">
                    <div>
                        <label class="form-label">{{ __('core/base::general.log_file') }}</label>
                        <select name="file" class="form-select">
                            @foreach($logFiles as $file)
                                <option value="{{ $file['name'] }}" @selected($selectedFile === $file['name'])>
                                    {{ $file['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">{{ __('core/base::general.log_level') }}</label>
                        <select name="level" class="form-select">
                            @foreach(['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY', 'WARNING', 'NOTICE', 'INFO', 'DEBUG', 'ALL'] as $option)
                                <option value="{{ $option }}" @selected($level === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">{{ __('Số dòng') }}</label>
                        <select name="limit" class="form-select">
                            @foreach([10, 25, 50, 100, 200] as $option)
                                <option value="{{ $option }}" @selected($limit === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-primary" type="submit">
                        {!! tabler_icon('refresh', ['class' => 'icon']) !!}
                        {{ __('core/base::general.update') }}
                    </button>
                </form>
            </x-ui::card>
        </div>

        <div class="col-md-9">
            <x-ui::card>
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            {!! tabler_icon('bug', ['class' => 'icon me-2']) !!}
                            {{ __('core/base::general.latest_errors') }}
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($selectedFile)
                                <span class="badge bg-muted-lt">{{ $selectedFile }}</span>
                                @can('system-logs.delete')
                                    <form method="POST" action="{{ route('core.system-logs.destroy') }}" onsubmit="return confirm('{{ __('core/base::general.delete_log_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="file" value="{{ $selectedFile }}">
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            {!! tabler_icon('trash', ['class' => 'icon']) !!}
                                            {{ __('core/base::general.delete_log') }}
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                </x-slot>

                @forelse($entries as $entry)
                    <div class="border rounded mb-3 overflow-hidden">
                        <div class="d-flex align-items-start justify-content-between gap-3 p-3 bg-muted-lt">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge @if(in_array($entry['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true)) bg-danger-lt text-danger @elseif($entry['level'] === 'WARNING') bg-warning-lt text-warning @else bg-info-lt text-info @endif">
                                        {{ $entry['level'] }}
                                    </span>
                                    <span class="text-muted small">{{ $entry['environment'] }}</span>
                                    <span class="text-muted small">{{ $entry['date'] }}</span>
                                </div>
                                <div class="fw-semibold text-break">{{ $entry['message'] }}</div>
                            </div>
                        </div>

                        @if(! empty($entry['stack']))
                            <pre class="m-0 p-3 bg-dark text-white small overflow-auto" style="max-height: 520px;"><code>{{ implode("\n", $entry['stack']) }}</code></pre>
                        @endif
                    </div>
                @empty
                    <div class="empty">
                        <div class="empty-icon">
                            {!! tabler_icon('file-search', ['class' => 'icon']) !!}
                        </div>
                        <p class="empty-title">{{ __('core/base::general.no_data') }}</p>
                    </div>
                @endforelse
            </x-ui::card>
        </div>
    </div>
</x-ui.layouts::app>
