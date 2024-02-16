@extends('core/base::error.error')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Sorry, you are not authorized to access this page.'))
