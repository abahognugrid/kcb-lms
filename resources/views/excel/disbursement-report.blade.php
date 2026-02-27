@extends('excel.layouts')

@section('content')
    <table id="report-table" class="table table-bordered">
        <thead>
            <tr>
                <th colspan="7" style="font-size: 20px; text-align: center; padding: 2px;">{{ $partnerName }}</th>
            </tr>
            <tr>
                <th colspan="7" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Disbursement Report</th>
            </tr>
            <tr>
                <th colspan="7" style="font-weight: bold; font-size: 14px; text-align: center; padding: 2px;">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
            </tr>
            <tr>
                <th colspan="7"></th>
            </tr>
            <tr>
                <th style="font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Loan #</th>
                <th style="font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Customer</th>
                <th style="text-align: right; font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Phone Number</th>
                <th style="font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Product Name</th>
                <th style="font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999; text-align: right">Term</th>
                <th style="text-align: right; font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Disbursement Date</th>
                <th style="text-align: right; font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">Amount</th>
            </tr>
        </thead>
        <tbody class="">
        @forelse ($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->customer->name }}</td>
                <td style="text-align: right;">{{ $record->customer->Telephone_Number }}</td>
                <td>{{ $record->loan_product->Name }}</td>
                <td style="text-align: right;">{{ $record->Term }}</td>
                <td style="text-align: right;">{{ $record->created_at->toDateString()}}</td>
                <td style="text-align: right;">{{ $record->Facility_Amount_Granted }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No records found</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <th style="border: 1px solid black; padding: 2px; font-weight: bold;">Totals</th>
                <th style="text-align: right; border: 1px solid black; padding: 2px; font-weight: bold;">{{ $records->count() }}</th>
                <th colspan="4" style="border: 1px solid black; padding: 2px; font-weight: bold;"></th>
                <th style="text-align: right; border: 1px solid black; padding: 2px; font-weight: bold;">{{ $records->sum('Facility_Amount_Granted') }}</th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
