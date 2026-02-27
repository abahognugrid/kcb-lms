@extends('layouts/contentNavbarLayout')
@section('title', 'Switch - Create')
@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Create New Switch</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('switches.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Partner <span class="small">- Leave blank for non payment
                                switch</span></label>
                        <select class="form-select" aria-label="Default select example" name="partner_id">
                            <option>Choose Partner</option>
                            @foreach ($partners as $partnerId => $partnerName)
                                <option value="{{ $partnerId }}" @selected(old('partner_id') === $partnerId)>{{ $partnerName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="Payment">Payment</option>
                            <option value="SMS">SMS</option>
                        </select>
                    </div>

                    <div id="sms-fields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label" for="sender_id">Sender ID</label>
                            <input type="text" id="sender_id" class="form-control" name="sender_id"
                                placeholder="atupdates" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="username">API Username</label>
                            <input type="text" id="username" class="form-control" name="username"
                                placeholder="atupdates" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">API Key</label>
                            <input type="password" id="password" class="form-control" name="password"
                                placeholder="atupdates" />
                        </div>
                    </div>

                    <script>
                        const category = document.getElementById('category');
                        const smsFields = document.getElementById('sms-fields');

                        category.addEventListener('change', function() {
                            if (this.value === 'SMS') {
                                smsFields.style.display = 'block';
                            } else {
                                smsFields.style.display = 'none';
                            }
                        });
                    </script>

                    <div class="mb-3">
                        <label for="environment" class="form-label">Environment</label>
                        <select name="environment" id="environment" class="form-control" required>
                            <option value="Production">Production</option>
                            <option value="Test">Test</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="On">On</option>
                            <option value="Off">Off</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-dark">Save</button>
                    <a href="{{ route('switches.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
