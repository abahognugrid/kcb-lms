@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">General Ledger Breakdown Report @if ($accountId = data_get($filters, 'accountId'))
                - {{ $records->first()?->account_name }}
            @endif
        </h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table id="report-table" class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">ID#</th>
                <th class="text-start">Account</th>
                <th class="text-start">Customer Name</th>
                <th class="text-start">Telephone Number</th>
                <th class="text-end">Entry Date</th>
                <th class="text-end">DR</th>
                <th class="text-end">CR</th>
                <th class="text-end">Balance</th>
            </tr>
            <tr>
                <th colspan="7">Opening Balance</th>
                <th class="text-end">{{ number_format(data_get($summary, 'opening_balance'), 2) }}</th>
            </tr>
        </thead>
        <tbody>
            @php $runningBalance = data_get($summary, 'opening_balance', 0); $accountType = $records->first()?->account?->type_letter; @endphp

            @forelse ($records as $record)
                @if (in_array($accountType, ['A', 'E']))
                    @php $runningBalance += $record->debit_amount; @endphp
                    @php $runningBalance -= $record->credit_amount @endphp
                @else
                    @php $runningBalance += $record->credit_amount; @endphp
                    @php $runningBalance -= $record->debit_amount @endphp
                @endif
                <tr>
                    <td class="">{{ $record->txn_id }}</td>
                    <td class="">{{ $record->account_name }}</td>
                    <td class="">{{ $record->customer?->name }}</td>
                    <td class="">{{ $record->customer?->Telephone_Number }}</td>
                    <td class="text-end">{{ $record->created_at }}</td>
                    <td class="text-end">{{ number_format($record->debit_amount) }}</td>
                    <td class="text-end">{{ number_format($record->credit_amount) }}</td>
                    <td class="text-end">{{ number_format($runningBalance, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td>No records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
        <tr>
            <th colspan="7">Closing Balance</th>
            <th class="text-end">{{ number_format(data_get($summary, 'closing_balance'), 2) }}</th>
        </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
