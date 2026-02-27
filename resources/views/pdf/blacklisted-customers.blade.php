@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ data_get($filters, 'partnerName') }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Black Listed Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Customer #</th>
                <th class="text-start">Customer Name</th>
                <th class="text-end">Phone Number</th>
                <th class="text-start">Amount Disbursed</th>
                <th class="text-start">Outstanding Balance</th>
                <th class="text-end">Date Blacklisted</th>
                <th class="text-start">Reason for Blacklisting</th>
                <th class="text-start">Blacklisted By</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->customer_id }}</td>
                    <td>{{ $record->customer_name }}</td>
                    <td class="text-end">{{ $record->telephone_number }}</td>
                    <td class="text-end"><x-money :value="$record->amount_disbursed" /></td>
                    <td class="text-end"><x-money :value="$record->amount_repaid" /></td>
                    <td class="text-end">{{ $record->date_blacklisted }}</td>
                    <td class="">{{ $record->reason_for_blacklisting }}</td>
                    <td class="">{{ $record->blacklisted_by_name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No black listed customers found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start">Totals</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th></th>
                <th class="text-end"><x-money :value="$records->sum('amount_disbursed')" /></th>
                <th class="text-end"><x-money :value="$records->sum('amount_repaid')" /></th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
