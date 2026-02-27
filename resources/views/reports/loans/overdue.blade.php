@extends('layouts/contentNavbarLayout')
@section('icon', 'menu-icon tf-icons bx bx-time-five')
@section('title', 'Loan Reports')
@section('content')
   <livewire:reports.loan-arrears-report/>
@endsection
