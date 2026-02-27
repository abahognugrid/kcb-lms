@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ data_get($filters, 'partnerName') }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Ageing Report</h4>
        <p style="margin-top: 0; font-size: 10px">As at: {{ data_get($filters, 'endDate') }}</p>
    </div>

    <table class="table table-bordered table-sm">
        <thead>
            <tr class="table-header">
                <th colspan="9"></th>
                <th colspan="5" class="text-center">Age Classes</th>
            </tr>
            <tr>
                <th class="text-nowrap">Loan #</th>
                <th class="text-nowrap">Name</th>
                <th class="text-nowrap text-end">Phone Number</th>
                <th class="text-end">Amount Disbursed</th>
                <th class="text-end">Date Disbursed</th>
                <th class="text-end">Maturity Date</th>
                <th class="text-end">Principal Outstanding</th>
                <th class="text-end">Principal In Arrears</th>
                <th class="text-end">Days in Arrears</th>
                @foreach (data_get($filters, 'ageingDays', []) as $provision)
                    <th class="text-nowrap text-end">{{ str_replace(' ', '', $provision->days) }}<br>days</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td class="text-nowrap">{{ $record->id }}</td>
                    <td class="text-nowrap">{{ $record->customer->name }}</td>
                    <td class="text-nowrap text-end">{{ $record->customer->Telephone_Number }}</td>
                    <td class="text-nowrap text-end"><x-money :value="$record->Facility_Amount_Granted" /></td>
                    <td class="text-nowrap text-end">{{ $record->Credit_Account_Date->format('d-m-Y') }}</td>
                    <td class="text-nowrap text-end">{{ $record->Maturity_Date->format('d-m-Y') }}</td>
                    <td class="text-nowrap text-end"><x-money :value="$record->schedule_sum_principal_remaining" /></td>
                    <td class="text-nowrap text-end"><x-money :value="$record->principal_in_arrears" /></td>
                    <td class="text-nowrap text-end">{{ $record->days_in_arrears < 0 ? abs($record->days_in_arrears) : 0 }}
                    </td>
                    <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_at_30" /></td>
                    <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_at_60" /></td>
                    <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_at_90" /></td>
                    <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_at_180" /></td>
                    <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_after_180" /></td>
                </tr>
            @empty
                <tr>
                    <td colspan="14">No records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-nowrap">Totals</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th class="text-end"></th>
                <th class="text-end"><x-money :value="$records->sum('Facility_Amount_Granted')" /></th>
                <th class="text-end"></th>
                <th class="text-end"></th>
                <th class="text-end"><x-money :value="$records->sum('schedule_sum_principal_remaining')" /></th>
                <th class="text-end"><x-money :value="$records->sum('principal_in_arrears')" /></th>
                <th class="text-end"></th>
                <th class="text-end"><x-money :value="$records->sum('principal_outstanding_at_30')" /></th>
                <th class="text-end"><x-money :value="$records->sum('principal_outstanding_at_60')" /></th>
                <th class="text-end"><x-money :value="$records->sum('principal_outstanding_at_90')" /></th>
                <th class="text-end"><x-money :value="$records->sum('principal_outstanding_at_180')" /></th>
                <th class="text-end"><x-money :value="$records->sum('principal_outstanding_after_180')" /></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
