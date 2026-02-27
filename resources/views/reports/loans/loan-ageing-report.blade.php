@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loan Reports')
@section('content')
<livewire:reports.loan-ageing-report />
@endsection
