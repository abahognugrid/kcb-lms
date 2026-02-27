@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Manage Partner')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manage {{ $partner->Institution_Name }}: {{ $partner->Identification_Code }}</h5>
                    <a href="/partners/{{ Auth::user()->partner_id }}" class="btn btn-dark">Edit Partner Details</a>
                </div>
            </div>

            <div class="card mt-4">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">API settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 border-end p-4 rounded">
                            <form action="/partners/{{ $partner->id }}/api" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-6">
                                    <label class="form-label" for="api_key">Api Key
                                        <p class="text-danger mb-0">
                                            This will override and disable the existing api key if any.
                                        </p>
                                    </label>
                                    <input type="text" class="form-control" id="api_key" name="api_key" disabled
                                        value="{{ old('api_key', $api_setting->api_key) }}">
                                    </input>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="expires_at">Api Key Expiration</label>
                                    @if ($api_setting->expires_at)
                                        <input type="date" class="form-control" id="expires_at" name="expires_at"
                                            value="{{ old('expires_at', \Carbon\Carbon::parse($api_setting->expires_at)->toDateString()) }}">
                                        </input>
                                    @else
                                        <input type="date" class="form-control" id="expires_at" name="expires_at"
                                            value="{{ old('expires_at', $api_setting->expires_at) }}">
                                        </input>
                                    @endif
                                    <div class="form-input">
                                        If left blank, the API key will not expire.
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark mt-4">Generate New Key</button>
                            </form>
                        </div>
                        <div class="col-6">
                            @if ($api_setting->has_been_used)
                                <h5 class="mb-0">In use since {{ $api_setting->created_at->format('d/m/Y') }} </h5>
                                <p class="text-primary">Last used {{ $api_setting->last_used_at->diffForHumans() }}</p>
                            @else
                                <h5 class="text-secondary mb-0">Never used</h5>
                            @endif
                            @if ($api_setting->api_key)
                                <hr class="mt-0 mb-0">
                                <p class="text-danger">This action cannot be undone and is permanent. Be sure before
                                    executing
                                    this.</p>
                                <form action="/partners/{{ $partner->id }}/api/delete" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger btn-sm">Revoke api key</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('partner.ova.create', $partner->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">OVA account settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 p-4 rounded">
                                <div class="mb-6">
                                    <h5 class="text-secondary">Airtel Money</h5>
                                    <hr>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="app-name">Unique App Name</label>
                                    <input type="text" id="app-name" name="app_name" class="form-control"
                                        value="{{ old('app_name', $partner->ova_setting?->app_name) }}" placeholder="" />
                                    <p>Share this app name with LMS admin to complete setup.</p>
                                </div>
                                <div class="mb-6">
                                    <h6 class="text-dark">Collections</h6>
                                    <hr>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="client-key">Client ID</label>
                                    <input type="text" id="client-key" name="client_key" class="form-control"
                                        value="{{ old('client_key', $partner->ova_setting?->client_key) }}" />
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="client-secret">Client Secret</label>
                                    <input type="password" id="client-secret" name="client_secret" class="form-control"
                                        value="{{ old('client_secret', $partner->ova_setting?->client_secret) }}" />
                                </div>
                                <div class="mb-6">
                                    <h6 class="text-dark">Disbursements</h6>
                                    <p>By filling the details below, you will be enabling Airtel Money Disbursements</p>
                                    <hr>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="pin">PIN</label>
                                    <input type="password" id="pin" name="pin" class="form-control"
                                        value="{{ old('pin', $partner->ova_setting?->pin) }}" />
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="airtel_public_key">Public Key - <small>Used to encrypt
                                            the PIN</small></label>
                                    <input type="password" id="airtel_public_key" name="airtel_public_key"
                                        class="form-control"
                                        value="{{ old('airtel_public_key', $partner->ova_setting?->airtel_public_key) }}"
                                        placeholder="Paste Airtel Public Key" />
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top">
                        <button type="submit" class="btn btn-dark mt-4">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
