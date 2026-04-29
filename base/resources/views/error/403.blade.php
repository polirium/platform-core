@extends('core/base::error.error')

@section('title', trans('core/base::errors.forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Sorry, you are forbidden from accessing this page.'))
