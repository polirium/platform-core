@props([
    'id' => 'modal-confirm',
    'title' => 'Bạn có chắc chắn?',
    'message' => 'Thao tác này không thể hoàn tác.',
    'confirmButton' => 'Đồng ý',
    'confirmColor' => 'danger',
    'cancelButton' => 'Hủy bỏ',
    'icon' => 'alert-triangle',
])

<div
    class="modal modal-blur fade"
    id="{{ $id }}"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    wire:ignore.self
>
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-{{ $confirmColor }}"></div>
            <div class="modal-body text-center py-4">
                @if($icon)
                <div class="text-{{ $confirmColor }} mb-2">
                    {!! tabler_icon($icon, ['class' => 'icon-lg']) !!}
                </div>
                @endif
                <h3>{{ $title }}</h3>
                <div class="text-secondary">
                    {{ $message }}
                    {{ $slot }}
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                {{ $cancelButton }}
                            </a>
                        </div>
                        <div class="col">
                            <button
                                type="button"
                                class="btn btn-{{ $confirmColor }} w-100"
                                data-bs-dismiss="modal"
                                {{ $attributes->thatStartWith('wire:click') }}
                                {{ $attributes->thatStartWith('x-on:click') }}
                                onclick="{{ $attributes->get('onclick') }}"
                            >
                                {{ $confirmButton }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
