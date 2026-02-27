@php
$suspendedInterest = data_get($filters, 'suspendedInterest', false);
@endphp
<table>
    <thead>
        <tr>
            <th colspan="13" style="text-align: center; font-size: 18px; font-weight: bold;">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="13" style="text-align: center; font-size: 14px; font-weight: bold;">Loan Arrears Report</th>
        </tr>
        <tr>
            <th colspan="13" style="text-align: center; font-size: 10px;">As at:
                {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th colspan="13"></th>
        </tr>
        <tr>
            <th colspan="4"></th>
            <th colspan="4" style="text-align: center; font-weight: bold;">Outstanding</th>
            <th colspan="{{ $suspendedInterest ? 1 : 4 }}" style="text-align: center; font-weight: bold;">@if($suspendedInterest) Suspended @else Arrears @endif</th>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th style="text-align: left; font-weight: bold; width: 80px;">Loan #</th>
            <th style="text-align: left; font-weight: bold; width: 180px;">Customer</th>
            <th style="text-align: left; font-weight: bold; width: 150px;">Phone Number</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Amount Disbursed</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Principal</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Interest</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Penalty</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Total</th>
            @if(! $suspendedInterest)
            <th style="text-align: right; font-weight: bold; width: 100px;">Principal</th>
            @endif
            <th style="text-align: right; font-weight: bold; width: 100px;">Interest</th>
            @if(! $suspendedInterest)
            <th style="text-align: right; font-weight: bold; width: 100px;">Penalty</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Total</th>
            @endif
            <th style="text-align: right; font-weight: bold; width: 100px;">Arrears Rate</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Days in Arrears</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Expiry Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->customer->name }}</td>
                <td>{{ $record->customer->Telephone_Number }}</td>
                <td style="text-align: right">{{ $record->Facility_Amount_Granted }}</td>
                <td style="text-align: right">{{ $record->schedule_sum_principal_remaining }}</td>
                <td style="text-align: right">{{ $record->schedule_sum_interest_remaining }}</td>
                <td style="text-align: right">{{ $record->penalty_amount }}</td>
                <td style="text-align: right">{{ $record->total_outstanding_amount }}</td>
                @if(! $suspendedInterest)
                <td style="text-align: right">{{ $record->total_principal_arrears }}</td>
                @endif
                <td style="text-align: right">{{ $record->total_interest_arrears }}</td>
                @if(! $suspendedInterest)
                <td style="text-align: right">{{ $record->penalty_arrears }}</td>
                <td style="text-align: right">{{ $record->total_arrears_amount }}</td>
                @endif
                <td style="text-align: right">{{ $record->arrears_rate }}%</td>
                <td style="text-align: right">{{ $record->arrear_days < 0 ? abs($record->arrear_days) : 0 }}</td>
                <td style="text-align: right">{{ $record->Maturity_Date->format('d-m-Y') }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Totals</th>
            <th style="text-align: right; font-weight:bold">{{ count($records) }}</th>
            <th></th>
            <th style="text-align: right; font-weight:bold">{{ $records->sum('Facility_Amount_Granted') }}</th>
            <th style="text-align: right; font-weight:bold">{{ $records->sum('schedule_sum_principal_remaining') }}</th>
            <th style="text-align: right; font-weight:bold">{{ $records->sum('schedule_sum_interest_remaining') }}</th>
            <th style="text-align: right; font-weight:bold">{{ $records->sum('penalty_amount') }}</th>
            <th style="text-align: right; font-weight:bold">{{ $records->sum('total_outstanding_amount') }}</th>
            @if(! $suspendedInterest)
            <th style="text-align: right; font-weight:bold">{{ $records->sum('total_principal_arrears') }}</th>
            @endif
            <th style="text-align: right; font-weight:bold">{{ $records->sum('total_interest_arrears') }}</th>
            @if(! $suspendedInterest)
            <th style="text-align: right; font-weight:bold">{{ $records->sum('penalty_arrears') }}</th>
            <th style="text-align: right; font-weight:bold">{{ $records->sum('total_arrears_amount') }}</th>
            @endif
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
<x-print-footer />
