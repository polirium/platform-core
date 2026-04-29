@props([
    'name' => 'avatar_file',
    'value' => null,
    'currentAvatar' => null,
    'label' => 'Avatar',
    'size' => 'xl', // sm, md, lg, xl, xxl
    'showRemove' => true,
    'accept' => 'image/jpeg,image/png,image/gif,image/webp',
    'maxSize' => 2048, // KB
    'required' => false,
])

@php
    $sizeMap = [
        'sm' => ['w' => 64, 'h' => 64, 'icon' => '1.5rem'],
        'md' => ['w' => 96, 'h' => 96, 'icon' => '2rem'],
        'lg' => ['w' => 120, 'h' => 120, 'icon' => '3rem'],
        'xl' => ['w' => 150, 'h' => 150, 'icon' => '4rem'],
        'xxl' => ['w' => 180, 'h' => 180, 'icon' => '4.5rem'],
    ];
    $sizeConfig = $sizeMap[$size] ?? $sizeMap['xl'];
@endphp

<div class="avatar-upload-wrapper" x-data="{
    hasPreview: {{ $value || $currentAvatar ? 'true' : 'false' }},
    fileName: ''
}">
    <!-- Avatar Preview -->
    <div class="avatar-upload-preview text-center">
        <div
            class="avatar-upload-container d-inline-block position-relative"
            style="cursor: pointer;"
            @click="$refs.{{ $name }}.click()"
        >
            <!-- Avatar Circle -->
            <div class="avatar avatar-{{ $size }} rounded-circle" style="width: {{ $sizeConfig['w'] }}px; height: {{ $sizeConfig['h'] }}px; overflow: hidden;">
                @if ($value)
                    <img
                        src="{{ $value->temporaryUrl() }}"
                        alt="Avatar preview"
                        class="rounded-circle"
                        style="width: {{ $sizeConfig['w'] }}px; height: {{ $sizeConfig['h'] }}px; object-fit: cover;"
                    >
                @elseif ($currentAvatar)
                    @if (str_starts_with($currentAvatar, 'http://') || str_starts_with($currentAvatar, 'https://'))
                        <img
                            src="{{ $currentAvatar }}"
                            alt="Current avatar"
                            class="rounded-circle"
                            style="width: {{ $sizeConfig['w'] }}px; height: {{ $sizeConfig['h'] }}px; object-fit: cover;"
                        >
                    @elseif (str_starts_with($currentAvatar, 'avatars/') || str_starts_with($currentAvatar, '/'))
                        <img
                            src="{{ asset('storage/' . ltrim($currentAvatar, '/')) }}"
                            alt="Current avatar"
                            class="rounded-circle"
                            style="width: {{ $sizeConfig['w'] }}px; height: {{ $sizeConfig['h'] }}px; object-fit: cover;"
                        >
                    @else
                        <img
                            src="{{ asset('storage/avatars/' . $currentAvatar) }}"
                            alt="Current avatar"
                            class="rounded-circle"
                            style="width: {{ $sizeConfig['w'] }}px; height: {{ $sizeConfig['h'] }}px; object-fit: cover;"
                        >
                    @endif
                @else
                    <span class="avatar avatar-{{ $size }} rounded-circle bg-primary-lt" style="width: {{ $sizeConfig['w'] }}px; height: {{ $sizeConfig['h'] }}px;">
                        {!! tabler_icon('user', ['class' => 'ti', 'style' => 'font-size: ' . $sizeConfig['icon'] . ';']) !!}
                    </span>
                @endif
            </div>

            <!-- Upload Badge -->
            <div
                class="avatar-upload-badge position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                style="width: 36px; height: 36px; transform: translate(25%, 25%); cursor: pointer;"
                @click="$refs.{{ $name }}.click()"
            >
                {!! tabler_icon('camera', ['class' => 'ti', 'style' => 'font-size: 1.2rem;']) !!}
            </div>
        </div>

        <!-- File Input -->
        <input
            type="file"
            x-ref="{{ $name }}"
            class="d-none"
            wire:model="{{ $name }}"
            accept="{{ $accept }}"
            @change="fileName = $refs.{{ $name }}.files[0]?.name || ''"
        >

        <!-- Helper Text -->
        <div class="mt-3">
            <div class="text-muted small">
                <span class="avatar-upload-click-hint" @click="$refs.{{ $name }}.click()" style="cursor: pointer; text-decoration: underline;">
                    {{ __('core/base::general.click_to_upload') }}
                </span>
                {{ __('core/base::general.or_drag_drop') }}
            </div>
            <div class="text-muted small">
                {{ __('core/base::general.accepted_formats') }}: JPG, PNG, GIF, WebP
            </div>
            <div class="text-muted small">
                {{ __('core/base::general.max_size') }}: {{ number_format($maxSize / 1024, 1) }}MB
            </div>
            @if($fileName)
                <div class="text-primary small mt-1">
                    <i class="ti ti-file"></i> {{ $fileName }}
                </div>
            @endif
        </div>

        <!-- Error Message -->
        @error($name)
            <div class="text-danger small mt-2">
                <i class="ti ti-alert-circle"></i> {{ $message }}
            </div>
        @enderror

        <!-- Loading State -->
        <div wire:loading wire:target="{{ $name }}" class="text-primary small mt-2">
            <i class="ti ti-loader-2 icon-spin"></i> {{ __('core/base::general.uploading') }}
        </div>
    </div>

    @once
    @push('styles')
    <style>
        .avatar-upload-wrapper {
            /* Component wrapper */
        }

        .avatar-upload-badge {
            transition: all 200ms ease;
        }

        .avatar-upload-badge:hover {
            transform: translate(25%, 25%) scale(1.1);
        }

        .avatar-upload-container:hover .avatar-upload-badge {
            background: var(--tblr-primary, #206bc4);
        }

        /* Drag and drop styles */
        .avatar-upload-container.drag-over {
            outline: 2px dashed var(--tblr-primary, #206bc4);
            outline-offset: 4px;
        }
    </style>
    @endpush
    @endonce
</div>
