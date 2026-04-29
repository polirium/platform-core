@extends('core/base::error.error')

@section('title', trans('core/base::errors.payment_required'))
@section('code', '402')
@section('message', trans('core/base::errors.payment_required_message'))
