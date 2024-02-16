@extends('core/base::error.error')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Sorry, the server encountered an error.'))
