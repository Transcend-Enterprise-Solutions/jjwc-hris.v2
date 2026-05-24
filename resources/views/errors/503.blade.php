@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Service Unavailable'))
@section('sub-message', __('The server is temporarily unable to handle the request. Please try again later.'))
