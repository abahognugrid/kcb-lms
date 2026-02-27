@extends('layouts.contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Partners')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <livewire:manage-partner-settings/>
        </div>
    </div>
@endsection
