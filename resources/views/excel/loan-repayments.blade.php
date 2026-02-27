<table>
    <thead>
        <tr>
            <th colspan="12" style="text-align: center; font-size: 18px; font-weight: bold;">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="12" style="text-align: center; font-size: 14px; font-weight: bold;">Loan Repayments Report</th>
        </tr>
        <tr>
            <th colspan="12" style="text-align: center; font-size: 10px">Period: {{ data_get($filters, 'startDate') }} to: {{ data_get($filters, 'endDate') }}</th>
        </tr>
        <tr class="table-header">
            <th colspan="5"></th>
            <th style="text-align: center; font-weight: bold;" colspan="4">Payments Due</th>
            <th style="text-align: center; font-weight: bold;" colspan="4">Payments Made</th>
            <th></th>
        </tr>
        <tr>
            <th style="font-weight: bold; width: 180px;">Loan #</th>
            <th style="font-weight: bold; width: 180px;">Customer</th>
            <th style="font-weight: bold; text-align: right; width: 120px;">Phone Number</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Loan Amount</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Last Payment Date</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Principal</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Interest</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Penalty</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Fees</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Principal</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Interest</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Penalty</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Fees</th>
            <th style="font-weight: bold; text-align: right; width: 120px">Total Paid</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->customer->name }}</td>
                <td style="text-align: right;">{{ $record->customer->Telephone_Number }}</td>
                <td style="text-align: right;">{{ $record->Credit_Amount }}</td>
                <td style="text-align: right;">{{ $record->last_payment_date }}</td>
                <td style="text-align: right;">{{ $record->principal_due }}</td>
                <td style="text-align: right;">{{ $record->interest_due }}</td>
                <td style="text-align: right;">{{ $record->penalty_due }}</td>
                <td style="text-align: right;">{{ $record->fees_due }}</td>
                <td style="text-align: right;">{{ $record->principal_paid }}</td>
                <td style="text-align: right;">{{ $record->interest_paid }}</td>
                <td style="text-align: right;">{{ $record->penalty_paid }}</td>
                <td style="text-align: right;">{{ $record->fees_paid }}</td>
                <td style="text-align: right;">{{ $record->total_paid }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12" style="text-align:center">No records found</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th style="font-weight:bold;">Totals</th>
            <th style="text-align: right; font-weight:bold;">{{ $records->count() }}</th>
            <th></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('Credit_Amount')" /></th>
            <th></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('principal_due')" /></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('interest_due')" /></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('penalty_due')" /></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('fees_due')" /></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('principal_paid')" /></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('interest_paid')" /></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('penalty_paid')" /></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('fees_paid')" /></th>
            <th style="text-align: right; font-weight:bold;"><x-money :value="$records->sum('total_paid')" /></th>
        </tr>
    </tfoot>
</table>
