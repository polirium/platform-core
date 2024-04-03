@props(["description" => null])
<label {{ $attributes?->class("form-label") }}>
    {{ $slot }}
    <span class="form-label-description">{{ $description }}</span>
</label>
