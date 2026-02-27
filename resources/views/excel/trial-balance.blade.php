<table>
    <thead>
        <tr>
            <th colspan="4">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="4">Trial Balance</th>
        </tr>
        <tr>
            <th colspan="4">As at: {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th>Account Code</th>
            <th>Account Name</th>
            <th>Debit</th>
            <th>Credit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
            <tr>
                <td>{{ $record->account->identifier }}</td>
                <td>{{ $record->account->name }}</td>
                <td>{{ $record->debit_amount }}</td>
                <td>{{ $record->credit_amount }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Totals</th>
            <th>{{ $records->sum('debit_amount') }}</th>
            <th>{{ $records->sum('credit_amount') }}</th>
        </tr>
    </tfoot>
</table>
