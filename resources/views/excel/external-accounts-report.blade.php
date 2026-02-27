@extends('excel.layouts')

@section('content')
    <table id="report-table" class="table table-bordered">
        <thead>
            <tr>
                <th colspan="5" style="font-size: 20px; text-align: center; padding: 2px;">{{ $partnerName }}</th>
            </tr>
            <tr>
                <th colspan="5" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">External Accounts Report</th>
            </tr>
            <tr>
                <th colspan="5" style="font-weight: bold; font-size: 14px; text-align: center; padding: 2px;">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
            </tr>
            <tr>
                <th colspan="5"></th>
            </tr>
            <tr>
                <th style="font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Date</th>
                <th style="font-weight: bold; border: 1px solid black; width: 200px; padding: 2px; background-color: #999999">Partner</th>
                <th style="font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Service Provider</th>
                <th style="text-align: right; font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Disbursement Account</th>
                <th style="text-align: right; font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Collection Account</th>
            </tr>
        </thead>
        <tbody class="">
        @forelse ($records as $record)
            <tr>
                <td style="border: 1px solid black; padding: 2px;">{{ $record->created_at->toDateString() }}</td>
                <td style="border: 1px solid black; padding: 2px;">{{ $record->partner->Institution_Name }}</td>
                <td style="border: 1px solid black; padding: 2px;">{{ $record->service_provider }}</td>
                <td style="border: 1px solid black; padding: 2px; text-align: right">{{ number_format($record->disbursement_account, 2) }}</td>
                <td style="border: 1px solid black; padding: 2px; text-align: right">{{ number_format($record->collection_account, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="border: 1px solid black; padding: 2px; text-align: center;">No external account balances found</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr>
            <th style="border: 1px solid black; padding: 2px; background-color: #cccccc">Totals</th>
            <th style="border: 1px solid black; padding: 2px; background-color: #cccccc; text-align: right">{{ $records->count() }} records</th>
            <th style="border: 1px solid black; padding: 2px; background-color: #cccccc"></th>
            <th style="border: 1px solid black; padding: 2px; background-color: #cccccc; text-align: right">{{ number_format($records->sum('disbursement_account'), 2) }}</th>
            <th style="border: 1px solid black; padding: 2px; background-color: #cccccc; text-align: right">{{ number_format($records->sum('collection_account'), 2) }}</th>
        </tr>
        </tfoot>
    </table>
@endsection
