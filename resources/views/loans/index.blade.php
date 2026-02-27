@extends('layouts.contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loans')
@section('content')
    <div class="col">
        <div class="nav-align-top mb-6">
            <div class="tab-content">
                <livewire:loan-datatable :status="'active'" />
            </div>
        </div>
    </div>
@endsection
