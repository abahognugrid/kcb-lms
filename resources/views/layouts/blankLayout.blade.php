@extends('layouts.commonMaster')

@section('layoutContent')
    <div class="container">
        @if (session()->has('success'))
            <div class="alert alert-success mt-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger mt-4">
                {{ session('error') }}
            </div>
        @endif
        @if (session()->has('info'))
            <div class="alert alert-primary mt-4">
                {{ session('info') }}
            </div>
        @endif
    </div>
    <!-- Content -->
    @yield('content')
    <!--/ Content -->
@endsection
