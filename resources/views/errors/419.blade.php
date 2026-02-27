@extends('layouts.blankLayout')

@section('title', '419 - Page Expired')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card px-sm-6 px-0">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                {{-- <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span> --}}
                                <span class="app-brand-text demo text-heading fw-bold">{{ config('app.name') }}</span>
                            </a>
                        </div>

                        <div class="text-center">
                            <h4 class="mb-1">Your session has expired due to inactivity! 👋</h4>
                            <p class="mb-6">Please log back in to your account. <span class="text-dark text-sm d-block mt-4">Redirecting you in 10 seconds..., or click button below</span></p>
                            <a href="{{ url('login') }}" class="d-flex justify-content-center">
                                <i class="bx bx-chevron-left scaleX-n1-rtl me-1"></i>
                                login
                            </a>
                        </div>


                        <script>
                            setInterval(function () {
                                window.location = '{{ url('login') }}';
                            }, 10000)
                        </script>
                    </div>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
@endsection
