<x-ui.layouts::app>
    <x-slot:title>{{ __('core/base::general.user_management') }}</x-slot:title>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('core/base::general.user_management') }}
                    </h2>
                </div>

            </div>
        </div>
    </div>

    <div class="crm-users-page">
        <!-- Main Content Card -->
        <div class="crm-main-card">
            <!-- Card Header -->
            <div class="crm-card-header d-flex justify-content-between align-items-center">
                <div class="crm-card-title">
                    <div class="crm-card-title-icon">
                        {!! tabler_icon('users', ['class' => 'ti']) !!}
                    </div>
                    <h2>{{ __('core/base::general.user_list') }}</h2>
                </div>
                <div class="card-actions">
                    <button type="button" class="btn btn-primary" data-action="show-modal" data-modal="modal-user">
                        {!! tabler_icon('plus', ['class' => 'icon']) !!}
                        <span class="btn-text">{{ __('core/base::general.add_user') }}</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>

            <!-- User Table -->
            @livewire('core/base::user-table')

            @livewire('core/base::user.modal')
            @livewire('core/base::user.modal.detail')
            @livewire('core/base::user.modal.delete')
        </div>
    </div>
</x-ui.layouts::app>
