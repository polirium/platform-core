@extends('core/base::error.error')

@section('title', trans('core/base::errors.too_many_requests'))
@section('code', '429')
@section('message', trans('core/base::errors.too_many_requests_message'))
