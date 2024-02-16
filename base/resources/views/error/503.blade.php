@extends('core/base::error.error')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Sorry, the service is unavailable.'))
