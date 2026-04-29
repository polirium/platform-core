@extends('core/base::error.error')

@section('title', trans('core/base::errors.error_page_title'))
@section('code', '404')
@section('message', trans('core/base::errors.page_not_found_message'))
