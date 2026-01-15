<div class="card-header">
    <div class="d-flex align-items-center gap-3">
        <span class="avatar avatar-sm bg-primary-lt">
            {!! tabler_icon('building-store', ['class' => 'icon']) !!}
        </span>
        <h3 class="card-title mb-0">{{ __('core/base::general.brands') }}</h3>
        <x-ui::badge color="azure" :lt="true">
            {{ $this->records->count() ?? 0 }} {{ __('core/base::general.brands') }}
        </x-ui::badge>
    </div>
    <div class="card-actions">
        <x-ui::button color="primary" icon="plus" @click="$dispatch('show-modal-create-brand')">
            {{ __('core/base::general.add_brand') }}
        </x-ui::button>
    </div>
</div>
