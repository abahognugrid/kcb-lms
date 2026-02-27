@extends('pdf.layouts')
@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Credit Limits Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th class="text-end">Phone Number</th>
                <th class="text-end">Total Loan Count</th>
                <th class="text-end">Total Loan Amount</th>
                <th class="text-end">Total Outstanding Balance</th>
                <th class="text-end">Credit Limit</th>
                <th class="text-end">Used Limit</th>
                <th class="text-end">Available Credit Limit</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->customer->name }}</td>
                    <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                    <td class="text-end">{{ $record->totalLoanCount() }}</td>
                    <td class="text-end">{{ 'UGX ' . number_format($record->totalLoanAmount()) }}</td>
                    <td class="text-end">{{ 'UGX ' . number_format($record->totalOutstandingBalance()) }}</td>
                    <td class="text-end">{{ 'UGX ' . number_format($record->credit_limit) }}</td>
                    <td class="text-end">{{ 'UGX ' . number_format($record->used_credit) }}</td>
                    <td class="text-end">{{ 'UGX ' . number_format($record->available_credit) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
