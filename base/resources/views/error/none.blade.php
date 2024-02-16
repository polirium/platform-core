@extends('core/base::error.error')

@section('title',$exception->getMessage())
@section('code', $exception->getStatusCode())
@section('message', $exception->getMessage())
