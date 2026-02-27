@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Customers')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Upload Customers (CSV)</h5>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group">
                        <a href="{{ route('customer.create') }}" class="btn btn-outline-dark">New Customer</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- File Upload Section -->
            <form action="{{ route('customer.upload.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="csvFile" class="form-label">Upload CSV File</label>
                    <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                    <div class="form-text">Please upload a CSV file containing customer data.</div>
                </div>
                <button type="submit" class="btn btn-dark">Upload</button>
            </form>

            <!-- Download Template Link -->
            <hr>
            <h6>Download Template</h6>
            <p>You can download the CSV template for uploading customers:</p>
            <a href="{{ asset('templates/customer_template.csv') }}" class="btn btn-outline-secondary" download>Download Template</a>
        </div>
    </div>
@endsection
