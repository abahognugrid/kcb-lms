<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th colspan="14" style="font-size: 20px; text-align: center; padding: 2px;">{{ data_get($filters, 'partnerName') }}</th>
        </tr>
        <tr>
            <th colspan="14" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Loan
                Ageing Report</th>
        </tr>
        <tr>
            <th colspan="14" style="font-weight: bold; font-size: 14px; text-align: center; padding: 2px;">As At:
                {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th colspan="14"></th>
        </tr>
        <tr class="table-header">
            <th colspan="9"></th>
            <th colspan="5" style="text-align:center; font-weight:bold;">Age Classes</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Loan #</th>
            <th style="font-weight: bold;">Name</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Phone Number</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Amount Disbursed</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Date Disbursed</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Maturity Date</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Principal Outstanding</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Principal in Arrears</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Days in Arrears</th>
            @foreach (data_get($filters, 'ageingDays', []) as $provision)
                <th style="width: 100px; text-align: right; font-weight: bold;">{{ str_replace(' ', '', $provision->days) }}<br>days</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($records as $record)
            <tr>
                <td class="text-nowrap">{{ $record->id }}</td>
                <td class="text-nowrap">{{ $record->customer->name }}</td>
                <td style="text-align: right;">{{ $record->customer->Telephone_Number }}</td>
                <td style="text-align: right;">{{ number_format($record->Facility_Amount_Granted) }}</td>
                <td style="text-align: right;">{{ $record->Credit_Account_Date->format('d-m-Y') }}</td>
                <td style="text-align: right;">{{ $record->Maturity_Date->format('d-m-Y') }}</td>
                <td style="text-align: right;">{{ number_format($record->schedule_sum_principal_remaining) }}</td>
                <td style="text-align: right;">{{ number_format($record->principal_in_arrears) }}</td>
                <td style="text-align: right;">{{ $record->days_in_arrears < 0 ? abs($record->days_in_arrears) : 0 }}
                </td>
                <td style="text-align: right;"><x-money :value="$record->principal_outstanding_at_30" /></td>
                <td style="text-align: right;"><x-money :value="$record->principal_outstanding_at_60" /></td>
                <td style="text-align: right;"><x-money :value="$record->principal_outstanding_at_90" /></td>
                <td style="text-align: right;"><x-money :value="$record->principal_outstanding_at_180" /></td>
                <td style="text-align: right;"><x-money :value="$record->principal_outstanding_after_180" /></td>
            </tr>
        @empty
            <tr>
                <td colspan="14" class="text-center">No records found</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th style="font-weight: bold;">Totals</th>
            <th style="text-align: right; font-weight: bold;">{{ $records->count() }}</th>
            <th style="text-align: right; font-weight: bold;"></th>
            <th style="text-align: right; font-weight: bold;"><x-money :value="$records->sum('Facility_Amount_Granted')" /></th>
            <th style="text-align: right; font-weight: bold;"></th>
            <th style="text-align: right; font-weight: bold;"></th>
            <th style="text-align: right; font-weight: bold;"><x-money :value="$records->sum('schedule_sum_principal_remaining')" /></th>
            <th style="text-align: right; font-weight: bold;"><x-money :value="$records->sum('principal_in_arrears')" /></th>
            <th style="text-align: right; font-weight: bold;"></th>
            <th style="text-align: right; font-weight: bold;"><x-money :value="$records->sum('principal_outstanding_at_30')" /></th>
            <th style="text-align: right; font-weight: bold;"><x-money :value="$records->sum('principal_outstanding_at_60')" /></th>
            <th style="text-align: right; font-weight: bold;"><x-money :value="$records->sum('principal_outstanding_at_90')" /></th>
            <th style="text-align: right; font-weight: bold;"><x-money :value="$records->sum('principal_outstanding_at_180')" /></th>
            <th style="text-align: right; font-weight: bold;"><x-money :value="$records->sum('principal_outstanding_after_180')" /></th>
        </tr>
    </tfoot>
</table>
<x-print-footer />
