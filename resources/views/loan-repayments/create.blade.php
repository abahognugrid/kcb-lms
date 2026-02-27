@extends('layouts.contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loan Repayments')
@section('content')
    <div class="col">
        <div class="nav-align-top mb-6">
            <div class="tab-content">
                <livewire:create-loan-repayments/>
            </div>
        </div>
    </div>
@endsection
