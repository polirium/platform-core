@props([
    'text' => null,
])

@if($text)
    <div class="form-text">
        {{ $text }}
    </div>
@endif
