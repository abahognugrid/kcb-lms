@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">External Accounts Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate']  }} to {{ $filters['endDate'] }}</p>
    </div>

    <table id="report-table" class="table table-bordered">
        <thead>
        <tr class="table-header">
            <th class="text-start">Date</th>
            <th class="text-start">Partner</th>
            <th class="text-start">Service Provider</th>
            <th class="text-end">Disbursement Account</th>
            <th class="text-end">Collection Account</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($records as $record)
            <tr>
                <td>{{ $record->created_at->toDateString() }}</td>
                <td>{{ $record->partner->Institution_Name }}</td>
                <td>{{ $record->service_provider }}</td>
                <td class="text-end"><x-money :value="$record->disbursement_account" /></td>
                <td class="text-end"><x-money :value="$record->collection_account" /></td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No external account balances found</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr>
            <th class="text-start">Totals</th>
            <th class="text-end">{{ $records->count() }} records</th>
            <th colspan="1"></th>
            <th class="text-end"><x-money :value="$records->sum('disbursement_account')" /></th>
            <th class="text-end"><x-money :value="$records->sum('collection_account')" /></th>
        </tr>
        </tfoot>
    </table>
    <x-print-footer/>
@endsection
