@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Pending Disbursement Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate']  }} to {{ $filters['endDate'] }}</p>
    </div>

    <table id="report-table" class="table table-bordered">
        <thead>
        <tr class="table-header">
            <th class="text-start">Loan App. #</th>
            <th class="text-start">Customer Name</th>
            <th class="text-end">Phone Number</th>
            <th class="text-end">Pending Amount</th>
            <th class="text-end">Approval Date</th>
            <th class="text-start">Approved By</th>
        </tr>
        </thead>
        <tbody class="">
        @forelse ($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->customer->name }}</td>
                <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                <td class="text-end">{{ $record->Amount }}</td>
                <td>{{ $record->Last_Status_Change_Date }}</td> <!-- todo: Use approved at on loan or statuses -->
                <td></td>
            </tr>
        @empty
            <tr>
                <td colspan="6">There are no pending disbursements.</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot class="fw-bold">
        <tr>
            <th class="text-start">Totals</th>
            <th class="text-end">{{ $records->count() }}</th>
            <th></th>
            <th class="text-end">{{ number_format($records->sum('Amount')) }}</th>
            <th colspan="2"></th>
        </tr>
        </tfoot>
    </table>
    <x-print-footer/>
@endsection
