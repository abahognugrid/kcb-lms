<table>
    <thead>
        <tr>
            <th colspan="8" style="text-align: center; font-weight: bold; font-size: 16px;">
                {{ $partnerName ?? 'Account Ledger Report' }}
            </th>
        </tr>
        <tr></tr>
        <tr style="font-weight: bold; background-color: #f8f9fa;">
            <th style="border: 1px solid #000;">Loan ID</th>
            <th style="border: 1px solid #000;">Payment Reference</th>
            <th style="border: 1px solid #000;">Customer Name</th>
            <th style="border: 1px solid #000;">Telephone Number</th>
            <th style="border: 1px solid #000;">Date</th>
            <th style="border: 1px solid #000;">Account Name</th>
            <th style="border: 1px solid #000;">DR</th>
            <th style="border: 1px solid #000;">CR</th>
            <th style="border: 1px solid #000;">Balance</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <th class="text-start" colspan="8">Opening Balance</th>
        <th class="text-end">{{ data_get($summary, 'opening_balance', 0) }}</th>
    </tr>
        @foreach($records as $record)
            <tr>
                <td style="border: 1px solid #000;">{{ $record['loan_id'] }}</td>
                <td style="border: 1px solid #000;">{{ $record['payment_reference'] }}</td>
                <td style="border: 1px solid #000;">{{ $record['customer_name'] }}</td>
                <td style="border: 1px solid #000;">{{ $record['telephone_number'] }}</td>
                <td style="border: 1px solid #000; text-align: center;">
                    {{ \Carbon\Carbon::parse($record['created_at'])->format('Y-m-d H:i') }}
                </td>
                <td style="border: 1px solid #000;">{{ $record['account_name'] }}</td>
                <td style="border: 1px solid #000; text-align: right;">
                    {{ $record['debit_amount'] > 0 ? $record['debit_amount'] : '-' }}
                </td>
                <td style="border: 1px solid #000; text-align: right;">
                    {{ $record['credit_amount'] > 0 ? $record['credit_amount'] : '-' }}
                </td>
                <td style="border: 1px solid #000; text-align: right;">
                    {{ $record['balance'] }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f8f9fa;">
            <td colspan="" style="border: 1px solid #000;">Total</td>
            <td></td>
            <td style="border: 1px solid #000; text-align: center;">{{ $records->count() }} entries</td>
            <td colspan="3"></td>
            <td style="border: 1px solid #000; text-align: right;">{{ $records->sum('debit_amount') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $records->sum('credit_amount')  }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ data_get($records->last(), 'balance') }}</td>
        </tr>
        <tr>
            <th class="text-start" colspan="8">Closing Balance</th>
            <th class="text-end">{{ data_get($summary, 'closing_balance', 0) }}</th>
        </tr>
    </tfoot>
</table>
