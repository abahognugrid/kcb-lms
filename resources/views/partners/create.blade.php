@extends('layouts/contentNavbarLayout')
@section('title', 'Partner - Create')
@section('content')
<div class="row">
    <div class="col mb-6 order-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">New Partner</h5>

            </div>
            <div class="d-flex align-items-start row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <livewire:partners.create-partner />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection