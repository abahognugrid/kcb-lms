@extends('layouts/contentNavbarLayout')

@section('title', 'Loan Ledger - ' . $loan->Credit_Account_Reference)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">Loan Ledger</h5>
                    <small class="text-muted">Complete transaction history for loan account</small>
                </div>
                <div>
                    <a href="{{ route('loan-accounts.show', $loan) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back bx-14px"></i> Back to Loan Details
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Loan Summary -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-lighter">
                            <div class="card-body">
                                <h6 class="card-title">Loan Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Account Reference:</strong></td>
                                        <td>{{ $loan->Credit_Account_Reference }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer:</strong></td>
                                        <td>{{ $loan->customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $loan->customer->Telephone_Number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Loan Product:</strong></td>
                                        <td>{{ $loan->loan_product->Name ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-lighter">
                            <div class="card-body">
                                <h6 class="card-title">Loan Summary</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Principal Amount:</strong></td>
                                        <td>{{ number_format($loan->Credit_Amount, 2) }} {{ $loan->Currency }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Disbursement Date:</strong></td>
                                        <td>{{ $loan->Credit_Account_Date?->format('d M Y') ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Maturity Date:</strong></td>
                                        <td>{{ $loan->Maturity_Date?->format('d M Y') ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            {{ \App\Enums\LoanAccountType::formattedName($loan->Credit_Account_Status) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Livewire Component -->
                @livewire('reports.loan-ledger-report', ['loanId' => $loan->id])
            </div>
        </div>
    </div>
</div>
@endsection
