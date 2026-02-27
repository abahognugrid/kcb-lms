<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 18px; font-weight: bold;">
                {{ data_get($filters, 'partnerName') }}</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 14px; font-weight: bold;">Credit Limit Report</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 10px; font-weight: bold;">Period:
                {{ data_get($filters, 'startDate') }} to {{ data_get($filters, 'endDate') }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; width: 180px">Customer Name</th>
            <th class="text-align: right; font-weight: bold; width: 180px">Phone Number</th>
            <th class="text-align: right; font-weight: bold; width: 180px">Total Loan Count</th>
            <th class="text-align: right; font-weight: bold; width: 180px">Total Loan Amount</th>
            <th class="text-align: right; font-weight: bold; width: 180px">Total Outstanding Balance</th>
            <th class="text-align: right; font-weight: bold; width: 180px">Credit Limit</th>
            <th class="text-align: right; font-weight: bold; width: 180px">Used Limit</th>
            <th class="text-align: right; font-weight: bold; width: 180px">Available Credit Limit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->customer->name }}</td>
                <td class="text-align: right">{{ $record->customer->Telephone_Number }}</td>
                <td class="text-align: right">{{ $record->totalLoanCount() }}</td>
                <td class="text-align: right">{{ 'UGX ' . number_format($record->totalLoanAmount()) }}</td>
                <td class="text-align: right">{{ 'UGX ' . number_format($record->totalOutstandingBalance()) }}</td>
                <td class="text-align: right">{{ 'UGX ' . number_format($record->credit_limit) }}</td>
                <td class="text-align: right">{{ 'UGX ' . number_format($record->used_credit) }}</td>
                <td class="text-align: right">{{ 'UGX ' . number_format($record->available_credit) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
