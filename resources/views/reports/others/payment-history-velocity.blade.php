@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Payment History Velocity')

@section('content')
    @livewire('other-reports.payment-history-velocity-report', ['loanId' => $loan?->id])
@endsection
