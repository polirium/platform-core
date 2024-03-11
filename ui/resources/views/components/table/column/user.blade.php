@props([
    'avatar' => null,
    'name' => null,
    'email' => null,
])

<div class="d-flex py-1 align-items-center">
    <span class="avatar me-2" style="background-image: url({{ $avatar }})"></span>
    <div class="flex-fill">
        <div class="font-weight-medium">{{ $name }}</div>
        <div class="text-secondary"><a href="#" class="text-reset">{{ $email }}</a></div>
    </div>
</div>
