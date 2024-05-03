@if ($type == 'button')
    <button {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@elseif($type == 'link')
    <a {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@endif
