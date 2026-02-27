<table>
    <thead>
        <tr>
            <th colspan="11" style="text-align: center; font-size: 18px; font-weight: bold;">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-size: 14px; font-weight: bold;">Outstanding Loans Report
            </th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-size: 10px;">Period:
                {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr>
            <th style="text-align: left; font-weight: bold; width: 80px;">Loan #</th>
            <th style="text-align: left; font-weight: bold; width: 180px;">Customer</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Phone Number</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Amount Disbursed</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Date Disbursed</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Expiry Date</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Days to Expiry</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Principal</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Interest</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Penalty</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Total Balance</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Arrears Amount</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Pending Due</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td style="text-align: left">{{ $record->id }}</td>
                <td style="text-align: left">{{ $record->customer->name }}</td>
                <td style="text-align: right">{{ $record->customer->Telephone_Number }}</td>
                <td style="text-align: right">{{ number_format($record->Facility_Amount_Granted) }}</td>
                <td style="text-align: right">{{ $record->Credit_Account_Date->format('d-m-Y') }}</td>
                <td style="text-align: right">{{ $record->Maturity_Date->format('d-m-Y') }}</td>
                <td style="text-align: right">{{ $record->days_to_expiry }}</td>
                <td style="text-align: right">{{ number_format($record->schedule_sum_principal_remaining) }}</td>
                <td style="text-align: right">{{ number_format($record->schedule_sum_interest_remaining) }}</td>
                <td style="text-align: right">{{ number_format($record->penalty_amount) }}</td>
                <td style="text-align: right">
                    {{ number_format($record->schedule_sum_total_outstanding + $record->penalty_amount) }}
                </td>
                <td style="text-align: right">{{ number_format($record->total_past_due) }}</td>
                <td style="text-align: right">{{ number_format($record->total_pending_due) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Totals</th>
            <th style="text-align: right; font-weight:bold">{{ count($records) }}</th>
            <th></th>
            <th style="text-align: right; font-weight:bold"><x-money :value="$records->sum('Facility_Amount_Granted')" /></th>
            <th style="text-align: right; font-weight:bold"></th>
            <th style="text-align: right; font-weight:bold"></th>
            <th style="text-align: right; font-weight:bold"></th>
            <th style="text-align: right; font-weight:bold"><x-money :value="$records->sum('schedule_sum_principal_remaining')" /></th>
            <th style="text-align: right; font-weight:bold"><x-money :value="$records->sum('schedule_sum_interest_remaining')" /></th>
            <th style="text-align: right; font-weight:bold"><x-money :value="$records->sum('penalty_amount')" /></th>
            <th style="text-align: right; font-weight:bold"><x-money :value="$records->sum('schedule_sum_total_outstanding') + $records->sum('penalty_amount')" /></th>
            <th style="text-align: right; font-weight:bold"><x-money :value="$records->sum('total_past_due')" /></th>
            <th style="text-align: right; font-weight:bold"><x-money :value="$records->sum('total_pending_due')" /></th>
        </tr>
    </tfoot>
</table>
<x-print-footer />
