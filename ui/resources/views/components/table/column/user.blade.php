@props([
    'avatar' => null,
    'name' => null,
    'email' => null,
])

<div class="crm-user-cell">
    @if($avatar)
        <span class="crm-user-avatar" style="background-image: url({{ $avatar }})"></span>
    @else
        <span class="crm-user-avatar">{{ mb_strtoupper(mb_substr($name ?? '?', 0, 1)) }}</span>
    @endif
    <div class="crm-user-info">
        <span class="crm-user-name">{{ $name }}</span>
        <span class="crm-user-email">{{ $email }}</span>
    </div>
</div>
