@extends('layouts.contentNavbarLayout')
@section('title', 'Switch - Edit')
@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Edit Switch</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('switches.update', $switch) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $switch->name }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Partner </label>
                        <select class="form-select" aria-label="Default select example" name="partner_id">
                            @foreach ($partners as $partner)
                                <option value="{{ $partner->id }}"
                                    {{ $switch->partner_id == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->Institution_Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="Payment" {{ $switch->category == 'Payment' ? 'selected' : '' }}>Payment</option>
                            <option value="SMS" {{ $switch->category == 'SMS' ? 'selected' : '' }}>SMS</option>
                        </select>
                    </div>
                    <div id="sms-fields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label" for="sender_id">Sender ID</label>
                            <input type="text" id="sender_id" class="form-control" name="sender_id"
                                placeholder="atupdates" value="{{ $switch->sender_id }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="username">API Username</label>
                            <input type="text" id="username" class="form-control" name="username"
                                value="{{ $switch->username }}" placeholder="atupdates" />
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="password">API Key</label>
                            <input type="password" id="password" class="form-control" value="{{ $switch->password }}"
                                name="password" placeholder="atupdates" />
                        </div>
                    </div>
                    <script>
                        const category = document.getElementById('category');
                        const smsFields = document.getElementById('sms-fields');

                        function toggleSmsFields() {
                            if (category.value === 'SMS') {
                                smsFields.style.display = 'block';
                            } else {
                                smsFields.style.display = 'none';
                            }
                        }

                        // Run on page load
                        toggleSmsFields();

                        // Run when dropdown changes
                        category.addEventListener('change', toggleSmsFields);
                    </script>


                    <button type="submit" class="btn btn-dark">Update</button>
                    <a href="{{ route('switches.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

@endsection
