<table>
    <thead>
        <tr>
            <th colspan="8" style="font-size: 18px; font-weight: bold; text-align: center;">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="8" style="font-size: 14px; font-weight: bold; text-align: center;">General Ledger Breakdown
            </th>
        </tr>
        <tr>
            <th colspan="8" style="font-size: 10px; text-align: center;">Period: {{ $filters['startDate'] }} to
                {{ $filters['endDate'] }}</th>
        </tr>
        <tr class="table-header">
            <th style="font-weight: bold; text-align: left; width: 150px;">ID#</th>
            <th style="font-weight: bold; text-align: left; width: 200px;">Account</th>
            <th style="font-weight: bold; text-align: left; width: 200px;">Customer Name</th>
            <th style="font-weight: bold; text-align: left; width: 150px;">Telephone Number</th>
            <th style="font-weight: bold; text-align: right; width: 130px;">Entry Date</th>
            <th style="font-weight: bold; text-align: right; width: 100px;">DR</th>
            <th style="font-weight: bold; text-align: right; width: 100px;">CR</th>
            <th style="font-weight: bold; text-align: right; width: 100px;">Balance</th>
        </tr>
        <tr>
            <th colspan="7" style="font-weight: bold;">Opening Balance</th>
            <th style="font-weight: bold; text-align: right; width: 100px;">{{ number_format(data_get($summary, 'opening_balance'), 2) }}</th>
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
                <td>{{ $record->txn_id }}</td>
                <td>{{ $record->account_name }}</td>
                <td>{{ $record->customer?->name }}</td>
                <td>{{ $record->customer?->Telephone_Number }}</td>
                <td style="text-align: right">{{ $record->created_at }}</td>
                <td style="text-align: right">{{ $record->debit_amount }}</td>
                <td style="text-align: right">{{ $record->credit_amount }}</td>
                <td style="text-align: right">{{ $runningBalance }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8">No records found</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="7" style="font-weight: bold;">Closing Balance</th>
            <th style="font-weight: bold; text-align: right; width: 100px;">{{ number_format(data_get($summary, 'closing_balance'), 2) }}</th>
        </tr>
    </tfoot>
</table>
<x-print-footer />
