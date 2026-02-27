@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money')
@section('title', 'Loan Reports')
@section('content')
<livewire:reports.blacklisted-customers-report />
@endsection
