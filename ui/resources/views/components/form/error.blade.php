@props([
    'message' => null,
])

@if($message ??= ($errors ?? null)?->first($attribute ?? null))
    <div class="invalid-feedback d-block">
        {!! $message !!}
    </div>
@endif
