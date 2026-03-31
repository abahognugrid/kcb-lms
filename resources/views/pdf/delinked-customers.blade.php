@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ data_get($filters, 'partnerName') }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Delinked Customers Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Customer #</th>
                <th class="text-start">First Name</th>
                <th class="text-start">Last Name</th>
                <th class="text-end">Phone Number</th>
                <th class="text-start">Amount Disbursed</th>
                <th class="text-start">Outstanding Balance</th>
                <th class="text-end">Delinked At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->First_Name }}</td>
                    <td>{{ $record->Last_Name }}</td>
                    <td class="text-end">{{ $record->telephone_number }}</td>
                    <td class="text-end"><x-money :value="$record->amount_disbursed" /></td>
                    <td class="text-end"><x-money :value="$record->amount_repaid" /></td>
                    <td class="text-end">{{ $record->date_delinked }}</td>
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
                <th></th>
                <th class="text-end"><x-money :value="$records->sum('amount_disbursed')" /></th>
                <th class="text-end"><x-money :value="$records->sum('amount_repaid')" /></th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
