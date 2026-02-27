@extends('layouts.blankLayout')

@section('title', 'Login')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('page-script')
@vite(['resources/assets/js/csrf-refresh.js'])
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
                        <h4 class="mb-1">Welcome! 👋</h4>
                        <p class="mb-6">Please log-in to your account</p>
                    </div>

                    <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
                        @method('POST')
                        @csrf
                        @if ($errors->has('email'))
                        <span class="text-danger">
                            {{ $errors->first('email') }}
                        </span>
                        @endif
                        <div class="my-6 ">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email"
                                placeholder="Enter your email" autofocus>
                        </div>
                        <div class="mb-6 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="mb-8">
                            <div class="d-flex justify-content-between mt-8">
                                <!-- <div class="form-check mb-0 ms-2">
                                    <input class="form-check-input" type="checkbox" id="remember-me">
                                    <label class="form-check-label" for="remember-me">
                                        Remember Me
                                    </label>
                                </div> -->
                                <a href="{{ url('forgot-password') }}">
                                    <span>Forgot Password?</span>
                                </a>
                            </div>
                        </div>
                        <div class="mb-6">
                            <button class="btn btn-dark d-grid w-100" type="submit">Login</button>
                        </div>
                    </form>

                    <!-- <p class="text-center">
                        <span>New on our platform?</span>
                        <a href="{{ url('register') }}">
                            <span>Create an account</span>
                        </a>
                    </p> -->
                </div>
            </div>
        </div>
        <!-- /Register -->
    </div>
</div>

<script>
// Fallback CSRF token refresh mechanism
(function() {
    'use strict';

    // Simple fallback if main CSRF manager doesn't load
    function fallbackCsrfRefresh() {
        // Only run if main CSRF manager is not available
        if (typeof window.csrfManager !== 'undefined') {
            return;
        }

        console.log('Using fallback CSRF refresh mechanism');

        // Refresh token every 10 minutes as fallback
        setInterval(function() {
            fetch('/refresh-csrf-token', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok');
            })
            .then(function(data) {
                if (data.csrf_token) {
                    // Update meta tag
                    var metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag) {
                        metaTag.setAttribute('content', data.csrf_token);
                    }

                    // Update form token
                    var tokenInput = document.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        tokenInput.value = data.csrf_token;
                    }

                    console.log('CSRF token refreshed (fallback)');
                }
            })
            .catch(function(error) {
                console.error('Fallback CSRF refresh failed:', error);
            });
        }, 10 * 60 * 1000); // 10 minutes
    }

    // Initialize fallback after a short delay
    setTimeout(fallbackCsrfRefresh, 2000);
})();
</script>
@endsection
