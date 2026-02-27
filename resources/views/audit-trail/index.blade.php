@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-shield-alt-2')
@section('title', 'Audit Trail')
@section('content')
    <livewire:reports.audit-trail-report />
@endsection