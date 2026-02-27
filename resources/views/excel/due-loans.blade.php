<table>
    <thead>
        <tr>
            <th colspan="11" style="text-align: center; font-weight:bold;">Due Loans Report</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-weight:bold">{{ data_get($filters, 'partnerName') }}</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center">As at: {{ data_get($filters, 'endDate') }}
            </th>
        </tr>
        <tr>
            <th style="text-align: right">Loan#</th>
            <th style="text-align: left">Name</th>
            <th style="text-align: left">Phone Number</th>
            <th style="text-align: right">Date Disbursed</th>
            <th style="text-align: right">Amount Disbursed</th>
            <th style="text-align: right">Principal Balance</th>
            <th style="text-align: right">Amount Due</th>
            <th style="text-align: right">Amount Paid</th>
            <th style="text-align: right">Past Due</th>
            <th style="text-align: right">Pending Due</th>
            <th style="text-align: right">Expiry Date</th>
            <th style="text-align: right">Last Payment</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td style="text-align-right">{{ $record->id }}</td>
                <td>{{ $record->customer->name }}</td>
                <td>{{ $record->customer->Telephone_Number }}</td>
                <td style="text-align: right">{{ $record->Credit_Account_Date->toDateString() }}</td>
                <td style="text-align: right">{{ $record->Facility_Amount_Granted }}</td>
                <td style="text-align: right">{{ $record->schedule_sum_principal_remaining }}</td>
                <td style="text-align: right">{{ $record->schedule_sum_total_outstanding }}</td>
                <td style="text-align: right">
                    {{ $record->schedule_sum_total_payment - $record->schedule_sum_total_outstanding }}
                </td>
                <td style="text-align: right">{{ $record->past_due }}</td>
                <td style="text-align: right">{{ $record->pending_due }}</td>
                <td style="text-align: right">{{ $record->schedule_max_payment_due_date }}</td>
                <td style="text-align: right">{{ $record->last_payment_date?->format('d-m-Y') }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th style="font-weight:bold">Totals</th>
            <th style="text-align: right; font-weight:bold">{{ $records->count() }}</th>
            <th colspan="2"></th>
            <th style="text-align: right; font-weight:bold">
                {{ $records->sum('Facility_Amount_Granted') }}</th>
            <th style="text-align: right; font-weight:bold">
                {{ $records->sum('schedule_sum_principal_remaining') }}</th>
            <th style="text-align: right; font-weight:bold">
                {{ $records->sum('schedule_sum_total_outstanding') }}</th>
            <th style="text-align: right; font-weight:bold">
                {{ $records->sum('schedule_sum_total_payment') - $records->sum('schedule_sum_total_outstanding') }}
            </th>
            <th style="text-align: right; font-weight:bold">{{ $records->sum('past_due') }}</th>
            <th style="text-align: right; font-weight:bold">{{ $records->sum('pending_due') }}</th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
<x-print-footer/>
