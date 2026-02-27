@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Financial Reports - Cash flow statement')
@section('content')
    <livewire:reports.cash-flow-report/>
@endsection
