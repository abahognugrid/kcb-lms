@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Arrears Report</h4>
        <p style="margin-top: 0; font-size: 10px">As at: {{ $filters['endDate'] }}</p>
    </div>
    @php
    $suspendedInterest = data_get($filters, 'suspendedInterest', false);
    @endphp
    <table id="report-table" class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th colspan="4"></th>
                <th colspan="4" class="text-center">Outstanding</th>
                <th colspan="{{ $suspendedInterest ? 1 : 4 }}" class="text-center">{{ $suspendedInterest ? 'Suspended' : 'Arrears' }}</th>
                <th colspan="3"></th>
            </tr>
            <tr>
                <th>Loan #</th>
                <th>Customer</th>
                <th>Phone Number</th>
                <th class="text-end">Amount Disbursed</th>
                <th class="text-end">Principal</th>
                <th class="text-end">Interest</th>
                <th class="text-end">Penalty</th>
                <th class="text-end">Total</th>
                @if(! $suspendedInterest)
                <th class="text-end">Principal</th>
                @endif
                <th class="text-end">Interest</th>
                @if(! $suspendedInterest)
                <th class="text-end">Penalty</th>
                <th class="text-end">Total</th>
                @endif
                <th class="text-end">Arrears Rate</th>
                <th class="text-end">Days in Arrears</th>
                <th class="text-end">Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->customer->name }}</td>
                    <td>{{ $record->customer->Telephone_Number }}</td>
                    <td class="text-end"><x-money :value="$record->Facility_Amount_Granted" /></td>
                    <td class="text-end"><x-money :value="$record->schedule_sum_principal_remaining" /></td>
                    <td class="text-end"><x-money :value="$record->schedule_sum_interest_remaining" /></td>
                    <td class="text-end"><x-money :value="$record->penalty_amount" /></td>
                    <td class="text-end"><x-money :value="$record->total_outstanding_amount" /></td>
                    @if(! $suspendedInterest)
                    <td class="text-end"><x-money :value="$record->total_principal_arrears" /></td>
                    @endif
                    <td class="text-end"><x-money :value="$record->total_interest_arrears" /></td>
                    @if(! $suspendedInterest)
                    <td class="text-end"><x-money :value="$record->penalty_arrears" /></td>
                    <td class="text-end"><x-money :value="$record->total_arrears_amount" /></td>
                    @endif
                    <td class="text-end">{{ $record->arrears_rate }}%</td>
                    <td class="text-end">{{ $record->arrear_days < 0 ? abs($record->arrear_days) : 0 }}</td>
                    <td class="text-end">{{ $record->Maturity_Date->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $suspendedInterest ? 12 : 15 }}" class="text-center">No records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>Totals</th>
                <th class="text-end">{{ count($records) }}</th>
                <th></th>
                <th class="text-end"><x-money :value="$records->sum('Facility_Amount_Granted')" /></th>
                <th class="text-end">{{ number_format($records->sum('schedule_sum_principal_remaining')) }}</th>
                <th class="text-end">{{ number_format($records->sum('schedule_sum_interest_remaining')) }}</th>
                <th class="text-end">{{ number_format($records->sum('penalty_amount')) }}</th>
                <th class="text-end">{{ number_format($records->sum('total_outstanding_amount')) }}</th>
                @if(! $suspendedInterest)
                <th class="text-end">{{ number_format($records->sum('total_principal_arrears')) }}</th>
                @endif
                <th class="text-end">{{ number_format($records->sum('total_interest_arrears')) }}</th>
                @if(! $suspendedInterest)
                <th class="text-end">{{ number_format($records->sum('penalty_arrears')) }}</th>
                <th class="text-end">{{ number_format($records->sum('total_arrears_amount')) }}</th>
                @endif
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
