@extends('layouts/contentNavbarLayout')
@section('title', 'Customer - Create')
@section('content')
<div class="row">
    <div class="col mb-6 order-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">New Customer</h5>

            </div>
            <div class="d-flex align-items-start row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <livewire:customers.create-customer />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection