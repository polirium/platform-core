<div>
    {{-- Filter Panel --}}
    <x-ui::card>
        {{-- Header với icon --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                {!! tabler_icon('filter', ['class' => 'icon text-primary']) !!}
                <span class="fw-semibold">{{ __('core/base::general.filter') }}</span>
            </div>
        </div>

        {{-- Filter by User --}}
        <div class="mb-3">
            <label class="form-label small text-muted">{{ __('core/base::general.filter_by_user') }}</label>
            <select class="form-select" wire:model.live="search.user">
                <option value="">{{ __('core/base::general.all_users') }}</option>
                @foreach($users as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter by Action --}}
        <div class="mb-3">
            <label class="form-label small text-muted">{{ __('core/base::general.filter_by_action') }}</label>
            <select class="form-select" wire:model.live="search.action">
                <option value="">{{ __('core/base::general.all_actions') }}</option>
                @foreach($actions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Active filter indicator --}}
        @if (!empty($search['user']) || !empty($search['action']))
            <div class="p-2 bg-primary-lt rounded d-flex align-items-center justify-content-between">
                <span class="small text-primary">
                    {!! tabler_icon('filter-check', ['class' => 'icon icon-sm me-1']) !!}
                    {{ __('core/base::general.filter_active') }}
                </span>
                <button
                    class="btn btn-sm btn-ghost-danger btn-icon"
                    wire:click="clearFilter"
                    title="{{ __('core/base::general.clear_filter') }}"
                >
                    {!! tabler_icon('x', ['class' => 'icon icon-sm']) !!}
                </button>
            </div>
        @endif
    </x-ui::card>

    {{-- Info Card --}}
    <x-ui::card class="mt-3">
        <div class="d-flex align-items-center gap-2 mb-2">
            {!! tabler_icon('info-circle', ['class' => 'icon text-info']) !!}
            <span class="fw-semibold">{{ __('core/base::general.about_activity_log') }}</span>
        </div>
        <p class="text-muted small mb-0">
            {{ __('core/base::general.activity_log_description') }}
        </p>
    </x-ui::card>
</div>
