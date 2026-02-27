@extends('layouts/contentNavbarLayout')

@section('title', 'Account settings - Profile')

@section('page-script')
    @vite(['resources/assets/js/pages-account-settings-account.js'])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6 mb-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Update Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update-password', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <label for="password" class="form-label">Old password <x-required /></label>
                        <input class="form-control" type="password" id="password" name="old_password" required />
                        <label for="email" class="form-labe mt-4">New password <x-required /></label>
                        <input class="form-control" type="password" name="new_password" id="email" required />
                        <label for="email" class="form-labe mt-4">Confirm password
                            <x-required /></label>
                        <input class="form-control" type="password" name="new_password_confirmation" id="email"
                            required />
                        <div class="mt-6">
                            <button type="submit" class="btn btn-dark me-3">Update password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Profile Details</h5>
                    <p>Update your profile details below.</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        <label for="name" class="form-label">Name <x-required /></label>
                        <input class="form-control" type="text" id="name" name="name"
                            value="{{ old('name', $user->name) }}" />
                        <label for="email" class="form-labe mt-4">Email <x-required /></label>
                        <input class="form-control" type="email" name="email" id="email" required readonly
                            value="{{ old('email', $user->email) }}" />
                        <div class="my-7">
                            <button type="submit" class="btn btn-dark me-3">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="card mb-6">
            <div class="card-header mb-0">
                <h5 class="card-title">Enable 2FA</h5>
                <p>Two factor authentication adds an additional layer of security to your account.</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        @if ($user->has_2fa_enabled)
                            <p class="d-flex justify-center"><i class="bx bx-check-circle text-success"></i> &nbsp; 2FA
                                is currently enabled.</p>
                        @else
                            @if ($user->google2fa_secret && $user->google2fa_url)
                                @php
                                    $svgText = $user->google2fa_url;
                                    $base64Svg = base64_encode($svgText);
                                @endphp
                                <p>Scan the QR code with your authenticator app to enable 2FA. Download one
                                    <a target="_blank"
                                        href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">
                                        for Android here
                                    </a>
                                </p>
                                <img src="data:image/svg+xml;base64,{{ $base64Svg }}" alt="2fa QR code" />
                                <form action="{{ route('users.confirm-2fa-code', $user->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <label for="code" class="form-label">Code <x-required /></label>
                                    <input type="text" class="form-control mb-4" id="code" name="code" required>
                                    <button type="submit" class="btn btn-dark me-3">Verify 2FA
                                        <Code></Code></button>
                                </form>
                            @else
                                <form action="{{ route('users.enable-2fa', $user->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-dark me-3">Enable 2FA</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
