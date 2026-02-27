@php $isDisbursement = data_get($filters, 'isDisbursement', false) @endphp
<table>
    <thead>
        <tr>
            <th colspan="{{ $isDisbursement ? 6 : 10 }}" style="font-size: 20px; text-align: center;">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="{{ $isDisbursement ? 6 : 10 }}" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Daily Reconciliation Report</th>
        </tr>
        <tr>
            <th colspan="{{ $isDisbursement ? 6 : 10 }}" style="font-weight: bold; font-size: 12px; text-align: center; padding-top: 2px;">Period: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th colspan="{{ $isDisbursement ? 6 : 10 }}" style="font-weight: bold; font-size: 12px; text-align: center; padding-top: 2px;">Account Type: {{ $filters['accountType'] }}</th>
        </tr>
        <tr>
            <th style="font-weight: bold; width: 120px;">Loan #</th>
            <th style="font-weight: bold; width: 120px;">Payment Reference</th>
            <th style="font-weight: bold; width: 100px;">Customer Name</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Phone Number</th>
            <th style="font-weight: bold; width: 100px">Transaction Date</th>
            @if(! $isDisbursement)
            <th style="text-align: right; font-weight: bold; width: 100px;">Principal</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Interest</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Fees</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Penalty</th>
            @endif
            <th style="text-align: right; font-weight: bold; width: 100px;">Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th style="font-weight: bold;" colspan="{{ $isDisbursement ? 5 : 9 }}">Opening Balance</th>
            <th style="text-align: right; font-weight: bold;">{{ number_format(data_get($summary, 'opening_balance', 0))  }}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td>{{ $record['loan_id'] }}</td>
                <td>{{ $record['payment_reference'] }}</td>
                <td>{{ $record['customer_name'] }}</td>
                <td style="text-align: right;">{{ $record['transaction']->customer?->Telephone_Number }}</td>
                <td>{{ \Carbon\Carbon::parse($record['transaction_date'])->toDateTimeString() }}</td>
                @if(! $isDisbursement)
                <td style="text-align: right">{{ $record['principal_amount'] }}</td>
                <td style="text-align: right">{{ $record['interest_amount'] }}</td>
                <td style="text-align: right">{{ $record['fees_amount'] }}</td>
                <td style="text-align: right">{{ $record['penalty_amount'] }}</td>
                @endif
                <td style="text-align: right">{{ $record['total_amount'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $isDisbursement ? 6 : 10 }}">No records found</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
    <tr>
        <td style="font-weight: bold;">Sub Total</td>
        <td></td>
        <td style="font-weight: bold;">{{ $records->count() }}</td>
        <td colspan="2"></td>
        @if(! $isDisbursement)
            <td style="text-align: right; font-weight: bold;">{{ $records->sum('principal_amount') }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $records->sum('interest_amount') }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $records->sum('fees_amount') }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $records->sum('penalty_amount') }}</td>
        @endif
        <td style="text-align: right; font-weight: bold;">{{ $records->sum('total_amount') }}</td>
    </tr>
    <tr>
        <th style="text-align: left; font-weight: bold;" colspan="{{ $isDisbursement ? 5 : 9 }}">Closing Balance</th>
        <th style="text-align: right; font-weight: bold;">{{ data_get($summary, 'closing_balance', 0)  }}</th>
    </tr>
    </tfoot>
</table>

<x-print-footer />
