@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        @if ($showRecoveries)
            <h4 style="margin-top: 0; margin-bottom: 4px">Written Off Loans Recovered Report</h4>
        @else
            <h4 style="margin-top: 0; margin-bottom: 4px">Written Off Loans Report</h4>
        @endif
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table id="report-table" class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th>Loan #</th>
                <th>Customer Name</th>
                <th class="text-end">Phone Number</th>
                <th class="text-end">Amount Disbursed</th>
                <th class="text-end">Amount Written Off</th>
                <th class="text-end">Date Written Off</th>
                @if ($showRecoveries)
                    <th class="text-end">Amount Recovered</th>
                    <th class="text-end">Balance After Recovery</th>
                    <th class="text-end">Date Last Recovered</th>
                @endif
                <th>Written Off By</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->customer->name }}</td>
                    <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                    <td class="text-end"><x-money :value="$record->Facility_Amount_Granted" /></td>
                    <td class="text-end"><x-money :value="$record->Written_Off_Amount" /></td>
                    <td class="text-end">{{ $record->Written_Off_Date->format('d-m-Y') }}</td>
                    @if ($showRecoveries)
                        <td class="text-end"><x-money :value="$record->Written_Off_Amount_Recovered" /></td>
                        <td class="text-end"><x-money :value="$record->Written_Off_Amount - $record->Written_Off_Amount_Recovered" /></td>
                        <td class="text-end">{{ $record->Last_Recovered_At }}</td>
                    @endif
                    <td>{{ $record->writtenOffBy?->name }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Totals</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th></th>
                <th class="text-end"><x-money :value="$records->sum('Facility_Amount_Granted')" /></th>
                <th class="text-end"><x-money :value="$records->sum('Written_Off_Amount')" /> </th>
                <th></th>
                @if ($showRecoveries)
                    <th class="text-end"><x-money :value="$records->sum('Written_Off_Amount_Recovered')" /></th>
                    <th class="text-end"><x-money :value="$records->sum('schedule_sum_principal_remaining')" /></th>
                    <th></th>
                @endif
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
