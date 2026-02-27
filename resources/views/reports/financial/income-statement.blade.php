@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Financial Reports')
@section('content')
    <livewire:financial-reports.income-statement/>
@endsection
