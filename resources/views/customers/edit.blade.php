@extends('layouts/contentNavbarLayout')
@section('title', "Customer - Edit")
@section('content')
<div class="row">
    <div class="col mb-6 order-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit {{ $customer->First_Name . ' ' . $customer->Last_Name  }}</h5>
            </div>
            <div class="d-flex align-items-start row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <livewire:customers.edit-customer :customer="$customer" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection