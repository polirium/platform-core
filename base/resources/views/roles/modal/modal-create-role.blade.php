<div>
    <x-ui::modal id="modal-create-role" class="modal-fullscreen modal-fullscreen-down-md">
        <x-slot:header>
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-role-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="12" r="4"/>
                            <line x1="4.93" y1="4.93" x2="9.17" y2="9.17"/>
                            <line x1="14.83" y1="14.83" x2="19.07" y2="19.07"/>
                            <line x1="14.83" y1="9.17" x2="19.07" y2="4.93"/>
                            <line x1="4.93" y1="14.83" x2="9.17" y2="19.07"/>
                        </svg>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">
                            @if(isset($request['id']))
                                {{ __('core/base::general.edit_role_text') }} {{ __('core/base::general.role') }}
                            @else
                                {{ __('core/base::general.create_role_text') }} {{ __('core/base::general.role') }}
                            @endif
                        </h5>
                        <small class="text-muted">{{ __('core/base::general.role_information') }}</small>
                    </div>
                </div>
                <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </x-slot>

        <form wire:submit.prevent="submit" id="role-form">
            <x-ui::errors/>

            <div class="modal-role-content">
                {{-- Role Name Input --}}
                <div class="row mb-4">
                    <div class="col-lg-4">
                        <x-ui.form.input
                            wire:model.live="request.name"
                            :label="__('core/base::general.role_name')"
                            :placeholder="__('core/base::general.role_name_placeholder')"
                            icon="id"
                            required
                        />
                    </div>
                    <div class="col-lg-8">
                        <div class="alert alert-warning mb-0 h-100 d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 flex-shrink-0">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                            <small>{{ __('core/base::general.change_permissions_warning') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Permissions Grid --}}
                <div class="card">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <h6 class="mb-0 fw-bold">
                                {{ __('core/base::general.permissions') }}
                            </h6>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white" id="selected-count">{{ count($request['permissions'] ?? []) }}</span>
                            <small class="text-muted">/ {{ count($flags ?? []) }}</small>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            @foreach ($children['root'] ?? [] as $elementKey => $element)
                                <div class="col-lg-6 col-xl-4">
                                    <div class="permission-group-card">
                                        <div class="permission-group-header">
                                            <small class="fw-bold text-primary text-uppercase d-flex align-items-center justify-content-between">
                                                <span class="d-flex align-items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                                    </svg>
                                                    {{ trans($flags[$element]['name'] ?? $element) }}
                                                </span>
                                                <button type="button" class="btn-select-all" onclick="selectAllInGroup(this)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                                                        <path d="M9 10h6M9 14h6"/>
                                                    </svg>
                                                </button>
                                            </small>
                                        </div>
                                        <div class="permission-group-body">
                                            @if (isset($children[$element]) && count($children[$element]) > 0)
                                                @foreach ($children[$element] as $subKey => $subElements)
                                                    <div class="permission-item">
                                                        <input
                                                            type="checkbox"
                                                            class="permission-checkbox"
                                                            id="perm-{{ str_replace('.', '-', $subElements) }}"
                                                            value="{{ $flags[$subElements]['flag'] ?? $subElements }}"
                                                            wire:model.live="request.permissions"
                                                        >
                                                        <label class="permission-label" for="perm-{{ str_replace('.', '-', $subElements) }}">
                                                            {{ trans($flags[$subElements]['name'] ?? $subElements) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted small mb-0 text-center py-3">
                                                    {{ __('core/base::general.no_child_permissions') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hidden submit button --}}
            <button type="submit" class="d-none" id="role-form-submit"></button>
        </form>

        <x-slot:footer>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('core/base::general.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('role-form-submit').click()">
                    <span class="btn-content-normal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        {{ isset($request['id']) ? __('core/base::general.update') : __('core/base::general.create') }}
                    </span>
                    <span class="btn-content-loading d-none">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        {{ __('core/base::general.saving') }}
                    </span>
                </button>
            </div>
        </x-slot>
    </x-ui::modal>

    @script
    <script>
        function selectAllInGroup(btn) {
            const group = btn.closest('.permission-group-card');
            const checkboxes = group.querySelectorAll('.permission-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
                // Trigger change event for Livewire
                cb.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        const roleForm = document.getElementById('role-form');
        const submitBtn = document.querySelector('.btn-primary[onclick]');

        if (roleForm && submitBtn) {
            roleForm.addEventListener('submit', function(e) {
                const normalContent = submitBtn.querySelector('.btn-content-normal');
                const loadingContent = submitBtn.querySelector('.btn-content-loading');

                if (normalContent && loadingContent) {
                    normalContent.classList.add('d-none');
                    loadingContent.classList.remove('d-none');
                    submitBtn.disabled = true;
                }
            });

            Livewire.hook('message.processed', ({ component }) => {
                if (component.name === 'modal-create-role-component' ||
                    component.el.closest('[id^="modal-create-role"]')) {
                    const normalContent = submitBtn.querySelector('.btn-content-normal');
                    const loadingContent = submitBtn.querySelector('.btn-content-loading');
                    if (normalContent && loadingContent) {
                        normalContent.classList.remove('d-none');
                        loadingContent.classList.add('d-none');
                        submitBtn.disabled = false;
                    }
                }
            });
        }

        // Update selected count
        document.addEventListener('livewire:update', () => {
            const countBadge = document.getElementById('selected-count');
            if (countBadge && @this.request && @this.request.permissions) {
                countBadge.textContent = @this.request.permissions.length;
            }
        });
    </script>
    @endscript
</div>

@once
@push('styles')
<style>
    /* Close Modal Button */
    .btn-close-modal {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        padding: 0;
        background: transparent;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        color: #64748B;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .btn-close-modal:hover {
        background: #F1F5F9;
        border-color: #CBD5E1;
        color: #0F172A;
    }

    .btn-close-modal svg {
        width: 20px;
        height: 20px;
    }

    /* Modal Role Icon */
    .modal-role-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        color: white;
        border-radius: 12px;
        flex-shrink: 0;
    }

    /* Modal Content */
    .modal-role-content {
        height: calc(100vh - 240px);
        min-height: 400px;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    .modal-role-content::-webkit-scrollbar {
        width: 8px;
    }

    .modal-role-content::-webkit-scrollbar-track {
        background: #F1F5F9;
    }

    .modal-role-content::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
    }

    .modal-role-content::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    /* Permission Group Card */
    .permission-group-card {
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.2s ease;
        background: white;
        height: 100%;
    }

    .permission-group-card:hover {
        border-color: #3B82F6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
    }

    .permission-group-header {
        padding: 0.625rem 0.875rem;
        background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
        border-bottom: 1px solid #E2E8F0;
    }

    .btn-select-all {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        padding: 0;
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid rgba(37, 99, 235, 0.3);
        border-radius: 4px;
        color: #2563EB;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-select-all:hover {
        background: white;
        border-color: #2563EB;
    }

    .btn-select-all svg {
        width: 12px;
        height: 12px;
    }

    .permission-group-body {
        padding: 0.75rem 0.875rem;
        background: white;
    }

    /* Permission Item */
    .permission-item {
        display: flex;
        align-items: flex-start;
        gap: 0.625rem;
        padding: 0.375rem 0;
        position: relative;
        z-index: 1;
    }

    .permission-checkbox {
        flex-shrink: 0;
        width: 1rem;
        height: 1rem;
        margin-top: 0.125rem;
        cursor: pointer;
        position: relative;
        z-index: 2;
    }

    .permission-checkbox:checked {
        background-color: #2563EB;
        border-color: #2563EB;
    }

    .permission-checkbox:focus {
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        border-color: #2563EB;
    }

    .permission-label {
        flex: 1;
        font-size: 0.8125rem;
        line-height: 1.4;
        color: #475569;
        cursor: pointer;
        user-select: none;
    }

    .permission-label:hover {
        color: #0F172A;
    }

    /* Progress Bar */
    .progress-bar {
        background: linear-gradient(90deg, #2563EB, #3B82F6);
        transition: width 0.3s ease;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .modal-role-content {
            height: auto;
            max-height: 400px;
        }
    }

    /* Dark Mode */
    [data-bs-theme="dark"] .btn-close-modal {
        border-color: #475569;
        color: #94A3B8;
    }

    [data-bs-theme="dark"] .btn-close-modal:hover {
        background: #334155;
        border-color: #64748B;
        color: #F1F5F9;
    }

    [data-bs-theme="dark"] .permission-group-card {
        background: #1E293B;
        border-color: #334155;
    }

    [data-bs-theme="dark"] .permission-group-header {
        background: rgba(37, 99, 235, 0.2);
        border-color: #334155;
    }

    [data-bs-theme="dark"] .permission-group-body {
        background: #1E293B;
    }

    [data-bs-theme="dark"] .btn-select-all {
        background: rgba(30, 41, 59, 0.5);
        border-color: rgba(37, 99, 235, 0.3);
        color: #60A5FA;
    }

    [data-bs-theme="dark"] .btn-select-all:hover {
        background: #1E293B;
        border-color: #60A5FA;
    }

    [data-bs-theme="dark"] .permission-label {
        color: #CBD5E1;
    }

    [data-bs-theme="dark"] .permission-label:hover {
        color: #F1F5F9;
    }

    [data-bs-theme="dark"] .permission-checkbox {
        background-color: #0F172A;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .permission-checkbox:checked {
        background-color: #2563EB;
        border-color: #2563EB;
    }

    [data-bs-theme="dark"] .modal-role-content::-webkit-scrollbar-track {
        background: #1E293B;
    }

    [data-bs-theme="dark"] .modal-role-content::-webkit-scrollbar-thumb {
        background: #475569;
    }

    [data-bs-theme="dark"] .modal-role-content::-webkit-scrollbar-thumb:hover {
        background: #64748B;
    }

    /* Selected Count Badge */
    #selected-count {
        background-color: #2563EB !important;
        color: white !important;
    }

    [data-bs-theme="dark"] #selected-count {
        background-color: #3B82F6 !important;
        color: white !important;
    }
</style>
@endpush
@endonce
