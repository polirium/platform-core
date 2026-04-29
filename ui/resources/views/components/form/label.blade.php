@props([
    'for' => null,
    'required' => false,
    'optional' => false,
])

<label {{ $for ? 'for="' . $for . '"' : '' }} class="form-label {{ $required ? 'required' : '' }}">
    {{ $slot }}
    @if($required)
        <span class="text-danger ms-1">*</span>
    @endif
    @if($optional && !$required)
        <span class="text-muted ms-1">{{ __('core/base::general.optional') }}</span>
    @endif
</label>
