{{-- Table Page Layout Template --}}
{{-- Usage: @extends('core.ui::layouts.table-page') --}}

@props([
    'title' => null,
    'breadcrumb' => null,
    'showHeader' => true,
    'showFilters' => true,
    'showSearch' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ $attributes ?? '' }}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($title)
        <title>{{ $title }} - {{ config('app.name') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif
    {!! Assets::renderCss() !!}
    @stack('styles')
</head>
<body class="{{ config('core.ui.theme', 'light') }}">
    <div id="app">
        {{ $slot }}
    </div>

    {!! Assets::renderJs() !!}
    @stack('scripts')

    @if(isset($_SERVER['HTTP_X_LIVEWIRE']))
        <script src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js" defer></script>
    @endif
</body>
</html>
