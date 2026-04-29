@extends('core/ui::base.base')
@section('body-class', 'border-top-wide border-primary d-flex flex-column')
@section('content')
    {{ $slot }}
@endsection
