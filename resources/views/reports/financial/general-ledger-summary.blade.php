@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Other Reports')
@section('content')
    <livewire:other-reports.general-ledger-summary-report/>
@endsection
