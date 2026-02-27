@extends('excel.layouts')

@section('content')
    <table id="report-table" class="table table-bordered">
        <thead>
            <tr>
                <th colspan="{{ $columSpan }}" style="font-size: 20px; text-align: center; padding: 2px;">
                    {{ data_get($filters, 'partnerName') }}</th>
            </tr>
            <tr>
                <th colspan="{{ $columSpan }}"
                    style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">
                    Transactions Report @if ($status = data_get($filters, 'transactionStatus'))
                        - {{ strtoupper($status) }}
                    @endif
                </th>
            </tr>
            <tr>
                <th colspan="{{ $columSpan }}"
                    style="font-weight: bold; font-size: 14px; text-align: center; padding: 2px;">From:
                    {{ $filters['startDate'] }} To: {{ $filters['endDate'] }}</th>
            </tr>
            <tr>
                <th colspan="{{ $columSpan }}"></th>
            </tr>
            <tr>
                <th
                    style="font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; background-color: #999999">
                    Customer Name</th>
                <th
                    style="font-weight: bold; border: 1px solid black; width: 150px; padding: 2px; text-align: right; background-color: #999999">
                    Phone Number</th>
                <th
                    style="font-weight: bold; border: 1px solid black; width: 100px; padding: 2px; background-color: #999999">
                    Status</th>
                <th
                    style="font-weight: bold; border: 1px solid black; width: 100px; padding: 2px; background-color: #999999">
                    Type</th>
                <th
                    style="font-weight: bold; border: 1px solid black; width: 100px; padding: 2px; background-color: #999999">
                    Transaction ID</th>
                <th
                    style="font-weight: bold; border: 1px solid black; width: 100px; padding: 2px; background-color: #999999">
                    Payment Reference</th>
                <th
                    style="font-weight: bold; border: 1px solid black; width: 100px; padding: 2px; background-color: #999999">
                    Description</th>
                <th
                    style="font-weight: bold; border: 1px solid black; width: 120px; padding: 2px; text-align: right; background-color: #999999">
                    Amount</th>
                <th
                    style="font-weight: bold; border: 1px solid black; width: 180px; padding: 2px; text-align: right; background-color: #999999">
                    Transaction Date</th>
            </tr>
        </thead>
        <tbody class="">
            @forelse ($records as $record)
                <tr>
                    <td style="border: 1px solid black; padding: 2px;">{{ $record->customer->name }}</td>
                    <td style="text-align: right; border: 1px solid black; padding: 2px;">
                        {{ $record->customer->Telephone_Number }}</td>
                    <td style="border: 1px solid black; padding: 2px;">{{ $record->Status }}</td>
                    <td style="border: 1px solid black; padding: 2px;">{{ $record->Type }}</td>
                    <td style="border: 1px solid black; padding: 2px;">{{ $record->Provider_TXN_ID }}</td>
                    <td style="border: 1px solid black; padding: 2px;">{{ $record->Payment_Reference }}</td>
                    <td style="border: 1px solid black; padding: 2px;">{{ $record->Narration }}</td>
                    <td style="border: 1px solid black; padding: 2px; text-align: right;">{{ $record->Amount }}</td>
                    <td style="text-align: right;">{{ $record->created_at->toDateTimeString() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $columSpan }}">No records found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <th style="border: 1px solid black; padding: 2px; font-weight: bold;">Totals</th>
                <th style="text-align: right; border: 1px solid black; padding: 2px; font-weight: bold;">
                    {{ $records->count() }}</th>
                <th style="text-align: right; border: 1px solid black; padding: 2px; font-weight: bold;">
                    {{ $records->sum('Amount') }}</th>
                <th style="border: 1px solid black; padding: 2px; font-weight: bold;"></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
