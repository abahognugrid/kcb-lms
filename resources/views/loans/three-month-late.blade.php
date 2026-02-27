@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loans - 3 Month Late')
@section('content')
    <div class="table-wrapper">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">3 Month Late Loans</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Disbursement Date</th>
                            <th>Name</th>
                            <th>Loan#</th>
                            <th class="text-end">Principal</th>
                            <th class="text-end">Due</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Balance</th>
                            <th class="text-end">Pending Due</th>
                            <th>Last Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($loans) == 0)
                            <tr>
                                <td colspan="10">No loans found</td>
                            </tr>
                        @endif
                        @foreach ($loans as $loan)
                            <tr>
                                <td>{{ $loan->DisbursementDate }}</td>
                                <td>{{ $loan->FirstName . ' ' . $loan->LastName }}</td>
                                <td>{{ $loan->LoanID }}</td>
                                <td class="text-end">{{ number_format($loan->Principal, 2) }}</td>
                                <td class="text-end">{{ number_format($loan->Due, 2) }}</td>
                                <td class="text-end">{{ number_format($loan->Paid, 2) }}</td>
                                <td class="text-end">{{ number_format($loan->Balance, 2) }}</td>
                                <td class="text-end">{{ number_format($loan->PendingDue, 2) }}</td>
                                <td>{{ $loan->LastPayment ? $loan->LastPayment : 'N/A' }}</td>
                                <td>{{ convertAccountStatusCodeToText($loan->Credit_Account_Status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
    @include('reports.partials.sticky-table-styling')
@endsection
