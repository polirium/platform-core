@extends('core/base::error.error')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Sorry, you are forbidden from accessing this page.'))
