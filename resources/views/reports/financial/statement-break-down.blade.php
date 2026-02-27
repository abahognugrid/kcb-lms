@extends('layouts.contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money')
@section('title', 'Loan Reports - GL Statement')
@section('content')
    <livewire:other-reports.general-ledger-breakdown-report/>
@endsection
