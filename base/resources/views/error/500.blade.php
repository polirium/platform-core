@extends('core/base::error.error')

@section('title', trans('core/base::errors.server_error'))
@section('code', '500')
@section('message', trans('core/base::errors.server_error_message'))
