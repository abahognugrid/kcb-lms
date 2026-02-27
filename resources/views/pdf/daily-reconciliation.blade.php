@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Daily Reconciliation Report - {{ data_get($filters, 'accountType') }}</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate']  }} to {{ $filters['endDate'] }}</p>
    </div>
    @php $isDisbursement = data_get($filters, 'isDisbursement', false) @endphp
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <th class="text-start">Loan #</th>
            <th class="text-start">Payment Reference</th>
            <th class="text-start">Customer Name</th>
            <th class="text-end">Phone Number</th>
            <th class="text-end">Transaction Date</th>
            @if(! $isDisbursement)
                <th class="text-end">Principal</th>
                <th class="text-end">Interest</th>
                <th class="text-end">Fees</th>
                <th class="text-end">Penalty</th>
            @endif
            <th class="text-end">Total Amount</th>
        </tr>
        <tr class="table-header">
            <th class="text-start" colspan="{{ $isDisbursement ? 5 : 9 }}">Opening Balance</th>
            <th class="text-end">{{ number_format(data_get($summary, 'opening_balance', 0))  }}</th>
        </tr>
        </thead>
        <tbody>

        @forelse($records as $record)
            <tr>
                <td>{{ $record['loan_id'] }}</td>
                <td>{{ $record['payment_reference'] }}</td>
                <td>{{ $record['customer_name'] }}</td>
                <td class="text-end">{{ $record['transaction']->customer?->Telephone_Number }}</td>
                <td class="text-end">{{ \Carbon\Carbon::parse($record['transaction_date'])->toDateTimeString() }}</td>
                @if(! $isDisbursement)
                    <td class="text-end">{{ number_format($record['principal_amount']) }}</td>
                    <td class="text-end">{{ number_format($record['interest_amount']) }}</td>
                    <td class="text-end">{{ number_format($record['fees_amount']) }}</td>
                    <td class="text-end">{{ number_format($record['penalty_amount']) }}</td>
                @endif
                <td class="text-end">{{ number_format($record['total_amount']) }}</td>
            </tr>
        @empty
            <tr>
                <td class="text-center" colspan="{{ $isDisbursement ? 6 : 10 }}">No records found</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr>
            <td>Sub Total</td>
            <td></td>
            <td>{{ $records->count() }}</td>
            <td colspan="2"></td>
            @if(! $isDisbursement)
                <td class="text-end">{{ number_format($records->sum('principal_amount')) }}</td>
                <td class="text-end">{{ number_format($records->sum('interest_amount')) }}</td>
                <td class="text-end">{{ number_format($records->sum('fees_amount')) }}</td>
                <td class="text-end">{{ number_format($records->sum('penalty_amount')) }}</td>
            @endif
            <td class="text-end">{{ number_format($records->sum('total_amount')) }}</td>
        </tr>
        <tr>
            <th class="text-start" colspan="{{ $isDisbursement ? 5 : 9 }}">Closing Balance</th>
            <th class="text-end">{{ number_format(data_get($summary, 'closing_balance', 0))  }}</th>
        </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
