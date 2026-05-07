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

            <!-- Tabs -->
            <div class="card-tabs mb-3 pe-3 ps-3">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="active-users-tab" data-tab="active" type="button" role="tab" aria-controls="active-users" aria-selected="true">
                            {!! tabler_icon('users-group', ['class' => 'icon me-2']) !!}
                            {{ __('core/base::general.active_users') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="deleted-users-tab" data-tab="deleted" type="button" role="tab" aria-controls="deleted-users" aria-selected="false">
                            {!! tabler_icon('trash', ['class' => 'icon me-2']) !!}
                            {{ __('core/base::general.deleted_users') }}
                        </button>
                    </li>
                </ul>
            </div>

            <script>
                document.querySelectorAll('[data-tab]').forEach(button => {
                    button.addEventListener('click', function() {
                        const tab = this.getAttribute('data-tab');

                        // Update active tab styling
                        document.querySelectorAll('[data-tab]').forEach(b => {
                            b.classList.remove('active');
                            b.setAttribute('aria-selected', 'false');
                        });
                        this.classList.add('active');
                        this.setAttribute('aria-selected', 'true');

                        // Dispatch to Livewire
                        Livewire.dispatch('switch-user-tab', {
                            tab: tab
                        });
                    });
                });
            </script>

            <!-- User Table -->
            @livewire('core/base::user-table')

            @livewire('core/base::user.modal')
            @livewire('core/base::user.modal.detail')
            @livewire('core/base::user.modal.delete')
            @livewire('core/base::user.modal.restore')
            @livewire('core/base::user.modal.force-delete')
        </div>
    </div>
</x-ui.layouts::app>
