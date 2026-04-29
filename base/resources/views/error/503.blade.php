@extends('core/base::error.error')

@section('title', trans('core/base::errors.service_unavailable'))
@section('code', '503')
@section('message', trans('core/base::errors.service_unavailable_message'))
