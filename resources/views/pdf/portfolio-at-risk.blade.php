@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ data_get($filters, 'partnerName') }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Portfolio at Risk Report</h4>
        <p style="margin-top: 0; font-size: 10px">As at: {{ data_get($filters, 'endDate') }}</p>
    </div>

    <table class="table table-bordered table-sm">
        <thead>
        <tr>
            <th colspan="6"></th>
            <th colspan="11" class="text-center">Principal in Arrears</th>
        </tr>
        <tr>
            <th class="text-nowrap">Loan #</th>
            <th class="text-nowrap">Name</th>
            <th class="text-nowrap text-end">Phone Number</th>
            <th class="text-end">Amount Disbursed</th>
            <th class="text-end">Maturity Date</th>
            <th class="text-end">Principal Outstanding</th>
            <th class="text-end">Arrears Principal</th>
            @foreach(data_get($filters, 'ageingDays', []) as $provision)
                <th class="text-nowrap text-end">{{ $provision->days }} <br/>days</th>
                <th class="text-end">PAR</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @forelse($records as $record)
            <tr>
                <td class="text-nowrap">{{ $record->id }}</td>
                <td class="text-nowrap">{{ $record->customer->name }}</td>
                <td class="text-nowrap text-end">{{ $record->customer->Telephone_Number }}</td>
                <td class="text-nowrap text-end"><x-money :value="$record->Facility_Amount_Granted"/></td>
                <td class="text-nowrap text-end">{{ $record->Maturity_Date->format('d-m-Y') }}</td>
                <td class="text-nowrap text-end"><x-money :value="$record->schedule_sum_principal_remaining"/></td>
                <td class="text-nowrap text-end"><x-money :value="$record->principal_in_arrears"/></td>
                <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_at_30"/></td>
                <td class="text-nowrap text-end">{{ percentage($record->principal_outstanding_at_30, $record->schedule_sum_principal_remaining) }}%</td>
                <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_at_60"/></td>
                <td class="text-nowrap text-end">{{ percentage($record->principal_outstanding_at_60, $record->schedule_sum_principal_remaining) }}%</td>
                <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_at_90"/></td>
                <td class="text-nowrap text-end">{{ percentage($record->principal_outstanding_at_90, $record->schedule_sum_principal_remaining) }}%</td>
                <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_at_180"/></td>
                <td class="text-nowrap text-end">{{ percentage($record->principal_outstanding_at_180, $record->schedule_sum_principal_remaining) }}%</td>
                <td class="text-nowrap text-end"><x-money :value="$record->principal_outstanding_after_180"/></td>
                <td class="text-nowrap text-end">{{ percentage($record->principal_outstanding_after_180, $record->schedule_sum_principal_remaining) }}%</td>
            </tr>
        @empty
            <tr>
                <td colspan="17">No records found</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        @php
            $principal = $records->sum('principal_outstanding')
        @endphp
        <tr>
            <th class="text-nowrap">Totals</th>
            <th class="text-end">{{ $records->count() }}</th>
            <th class="text-end"></th>
            <th class="text-end"><x-money :value="$records->sum('Facility_Amount_Granted')"/></th>
            <th class="text-end"></th>
            <th class="text-end"><x-money :value="$principal"/></th>
            <th class="text-end"><x-money :value="$records->sum('principal_in_arrears')"/></th>
            <th class="text-end"><x-money :value="$totalAt30 = $records->sum('principal_outstanding_at_30')"/></th>
            <th class="text-end">{{ percentage($totalAt30, $principal) }}%</th>
            <th class="text-end"><x-money :value="$totalAt60 = $records->sum('principal_outstanding_at_60')"/></th>
            <th class="text-end">{{ percentage($totalAt60, $principal) }}%</th>
            <th class="text-end"><x-money :value="$totalAt90 = $records->sum('principal_outstanding_at_90')"/></th>
            <th class="text-end">{{ percentage($totalAt90, $principal) }}%</th>
            <th class="text-end"><x-money :value="$totalAt180 = $records->sum('principal_outstanding_at_180')"/></th>
            <th class="text-end">{{ percentage($totalAt180, $principal) }}%</th>
            <th class="text-end"><x-money :value="$totalAfter180 = $records->sum('principal_outstanding_after_180')"/></th>
            <th class="text-end">{{ percentage($totalAfter180, $principal) }}%</th>
        </tr>
        </tfoot>
    </table>
    <x-print-footer/>
@endsection
