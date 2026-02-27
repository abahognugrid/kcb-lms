@extends('layouts/blankLayout')
@section('title', 'Verify Two Factor Authentication')
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
                          <h4 class="mb-1">Welcome to {{ config('app.name') }}! 👋</h4>
                        <p class="mb-6">Please enter your Authenticator code below to continue.</p>
                        </div>

                        <form  class="mb-6" action="{{ route('users.verify-2fa') }}" method="POST">
                            @method('POST')
                            @csrf
                            <div class="mb-6">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code" name="code"
                                    placeholder="Enter your code" autofocus>
                            </div>
                            <div class="mb-6">
                                <button class="btn btn-dark d-grid w-100" type="submit">Verify 2fa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
@endsection
