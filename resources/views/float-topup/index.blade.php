@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Float Management')
@section('content')
    @if (!auth()->user()->is_admin)
        <div class="card mb-4">
            <div class="card-header">
                <h2><x-money :value="$total_float_balance" /></h2>
                <small>Total Float Balance</small>
            </div>
        </div>
    @endif
    @if (!auth()->user()->is_admin)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Float Topups</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('float-management.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="Amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="Amount" name="Amount" step="0.01" required>
                    </div>

                    <!-- Proof of Payment (File Upload) -->
                    <div class="mb-3">
                        <label for="Proof_Of_Payment" class="form-label">Proof of Payment (Image)</label>
                        <input type="file" class="form-control" id="Proof_Of_Payment" name="Proof_Of_Payment"
                            accept="image/*" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-dark">Submit Topup</button>
                </form>
            </div>
        </div>
    @endif
    @if (!auth()->user()->is_admin)
        <div class="card mt-4">
            <div class="card-body">
                <livewire:float-topup-datatable />
            </div>
        </div>
    @endif
    @if (auth()->user()->is_admin)
        <!-- Pending Topups -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Pending Float Topups</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Proof of Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pending_topups as $topup)
                            <tr>
                                <td>UGX {{ number_format($topup->Amount, 2) }}</td>
                                <td>
                                    <a href="{{ $topup->Proof_Of_Payment }}" target="_blank">View Proof</a>
                                </td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <form action="{{ route('float-management.approve', $topup->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-dark btn-sm">Approve</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
