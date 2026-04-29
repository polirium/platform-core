@extends('core/base::error.error')

@section('title', trans('core/base::errors.unauthorized'))
@section('code', '401')
@section('message', trans('core/base::errors.unauthorized_message'))
