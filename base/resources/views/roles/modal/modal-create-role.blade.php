<div>
    <form wire:submit.prevent="submit">
        <x-ui::modal id="modal-create-role" :header="isset($request['id']) ? __('Sửa vai trò') : __('Tạo vai trò mới')" class="modal-xl">
            <x-ui::errors/>

            <div class="row g-4">
                <!-- Left: Role Name -->
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="mb-3">
                                <i class="ti ti-id me-2"></i>
                                {{ __('Thông tin vai trò') }}
                            </h4>

                            <div class="mb-3">
                                <x-form::input
                                    wire:model.live="request.name"
                                    :label="__('Tên vai trò')"
                                    :placeholder="__('VD: Manager, Editor...')"
                                    required
                                />
                            </div>

                            <x-ui::alert color="warning" class="mb-0">
                                <i class="ti ti-alert-triangle me-1"></i>
                                {{ __('Thay đổi quyền có thể ảnh hưởng đến người dùng có vai trò này.') }}
                            </x-ui::alert>

                            <!-- Selected permissions count -->
                            <div class="mt-3 p-3 border rounded">
                                <div class="text-center">
                                    <div class="text-muted small mb-1">{{ __('Quyền đã chọn') }}</div>
                                    <div class="h2 mb-0 text-primary">{{ count($request['permissions']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Permissions Tree -->
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="ti ti-lock me-2"></i>
                                {{ __('Phân quyền') }}
                            </h4>
                        </div>
                        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                            <div class="row g-3">
                                @foreach ($children['root'] as $elementKey => $element)
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header py-2 bg-light">
                                                <h5 class="mb-0 d-flex align-items-center">
                                                    <i class="ti ti-folder me-2 text-primary"></i>
                                                    {{ trans($flags[$element]['name']) }}
                                                </h5>
                                            </div>
                                            <div class="card-body p-2">
                                                @if (isset($children[$element]))
                                                    @foreach ($children[$element] as $subKey => $subElements)
                                                        <label class="form-check mb-1">
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                value="{{ $flags[$subElements]['flag'] }}"
                                                                wire:model.live="request.permissions"
                                                            >
                                                            <span class="form-check-label">
                                                                {{ trans($flags[$subElements]['name']) }}
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                @else
                                                    <div class="text-muted small">{{ __('Không có quyền con') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">
                    {{ __('Hủy') }}
                </button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">
                        <i class="ti ti-device-floppy me-1"></i>
                        {{ isset($request['id']) ? __('Cập nhật') : __('Tạo mới') }}
                    </span>
                    <span wire:loading wire:target="submit">
                        <i class="ti ti-loader-2 icon-spin me-1"></i>
                        {{ __('Đang lưu...') }}
                    </span>
                </button>
            </x-slot>
        </x-ui::modal>
    </form>
</div>
