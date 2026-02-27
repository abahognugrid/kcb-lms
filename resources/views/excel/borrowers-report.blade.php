@extends('excel.layouts')

@section('content')
    <table id="report-table" class="table table-bordered">
        <thead>
        <tr>
            <th colspan="4" style="font-size: 20px; text-align: center; padding: 2px;">{{ $partner->Institution_Name }}</th>
        </tr>
        <tr>
            <th colspan="4" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Borrowers Report</th>
        </tr>
        <tr>
            <th colspan="4" style="font-weight: bold; font-size: 12px; text-align: center; padding-top: 2px;">From: {{ $filters['startDate']  }} to {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th colspan="4"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Customer Name</th>
            <th style="font-weight: bold; border: 1px solid black; width: 150px; padding: 2px; text-align: right; background-color: #999999">Phone Number</th>
            <th style="text-align: right; font-weight: bold; border: 1px solid black; width: 150px; padding: 2px; background-color: #999999">Loans Count</th>
            <th style="text-align: right; font-weight: bold; border: 1px solid black; width: 150px; padding: 2px; background-color: #999999">Amount Borrowed</th>
        </tr>
        </thead>
        <tbody class="">
        @forelse ($records as $record)
            <tr>
                <td style="border: 1px solid black; padding: 2px;">{{ $record->name }}</td>
                <td style="text-align: right; border: 1px solid black; padding: 2px;">{{ $record->Telephone_Number }}</td>
                <td style="border: 1px solid black; padding: 2px; text-align: right;">{{ $record->loans_count}}</td>
                <td style="border: 1px solid black; padding: 2px; text-align: right">{{ number_format($record->loans_sum_facility__amount__granted) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No borrowers found.</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <th style="border: 1px solid black; padding: 2px; font-weight: bold;">Totals</th>
                <th style="text-align: right; border: 1px solid black; padding: 2px; font-weight: bold;">{{ $records->count() }}</th>
                <th style="text-align: right; border: 1px solid black; padding: 2px; font-weight: bold;">{{ $records->sum('loans_count') }}</th>
                <th style="text-align: right; border: 1px solid black; padding: 2px; font-weight: bold;"><x-money :value="$records->sum('loans_sum_facility__amount__granted')"/></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer/>
@endsection
