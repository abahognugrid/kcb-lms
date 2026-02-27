@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Account Ledger Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
        @if(!empty($filters['search']))
            <p style="margin-top: 0; font-size: 10px">Search: {{ $filters['search'] }}</p>
        @endif
    </div>

    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Loan ID</th>
                <th class="text-end">Payment Reference</th>
                <th class="text-start">Customer Name</th>
                <th class="text-end">Telephone Number</th>
                <th class="text-end">Date</th>
                <th class="text-start">Account Name</th>
                <th class="text-end">DR</th>
                <th class="text-end">CR</th>
                <th class="text-end">Balance</th>
            </tr>
            <tr class="table-header">
                <th class="text-start" colspan="8">Opening Balance</th>
                <th class="text-end">{{ number_format(data_get($summary, 'opening_balance', 0)) }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record['loan_id'] }}</td>
                    <td class="text-end">{{ $record['payment_reference'] }}</td>
                    <td>{{ $record['customer_name'] }}</td>
                    <td class="text-end">{{ $record['telephone_number'] }}</td>
                    <td class="text-end">{{ \Carbon\Carbon::parse($record['created_at'])->format('Y-m-d H:i') }}</td>
                    <td>{{ $record['account_name'] }}</td>
                    <td class="text-end">
                        {{ $record['debit_amount'] > 0 ? number_format($record['debit_amount'], 2) : '-' }}
                    </td>
                    <td class="text-end">
                        {{ $record['credit_amount'] > 0 ? number_format($record['credit_amount'], 2) : '-' }}
                    </td>
                    <td class="text-end">{{ number_format(abs($record['balance']), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="9">No journal entries found for the selected criteria.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            @if($records->count() > 0)
                <tr>
                    <td>Total</td>
                    <td class="text-end">{{ $records->count() }} entries</td>
                    <td colspan="4"></td>
                    <td class="text-end">{{ number_format($records->sum('debit_amount'), 2) }}</td>
                    <td class="text-end">{{ number_format($records->sum('credit_amount'), 2) }}</td>
                    <td class="text-end">{{ number_format($records->last()['balance'], 2) }}</td>
                </tr>
                <tr>
                    <th class="text-start" colspan="8">Closing Balance</th>
                    <th class="text-end">{{ number_format(data_get($summary, 'closing_balance', 0)) }}</th>
                </tr>
            @endif
        </tfoot>
    </table>
    <x-print-footer />
@endsection
