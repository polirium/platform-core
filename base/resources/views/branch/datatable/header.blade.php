<div class="card-header">
    <div class="d-flex align-items-center gap-3">
        <span class="avatar avatar-sm bg-primary-lt">
            {!! tabler_icon('building', ['class' => 'icon']) !!}
        </span>
        <h3 class="card-title mb-0">{{ __('core/base::general.branches') }}</h3>
        <x-ui::badge color="azure" :lt="true" x-data="{ count: 0 }" x-init="document.addEventListener('branch-table:dataUpdated', (e) => count = e.detail.count)">
            <span x-text="count + ' {{ __('core/base::general.branches') }}'"></span>
        </x-ui::badge>
    </div>
    <div class="card-actions">
        @can('branches.create')
            <x-ui::button color="primary" icon="plus" wire:click="$dispatch('show-modal-create-branch')">
                {{ __('core/base::general.add_branch') }}
            </x-ui::button>
        @endcan
    </div>
</div>
