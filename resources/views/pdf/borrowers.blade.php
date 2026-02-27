@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partner->Institution_Name }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Borrowers Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate']  }} to {{ $filters['endDate'] }}</p>
    </div>

    <table class="table">
        <thead>
        <tr class="table-header">
            <th class="text-start">Customer Name</th>
            <th class="text-end">Phone Number</th>
            <th class="text-end">Loan Count</th>
            <th class="text-end">Amount Borrowed</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($records as $record)
            <tr>
                <td class="text-nowrap">{{ $record->name }}</td>
                <td class="text-nowrap text-end">{{ $record->Telephone_Number }}</td>
                <td class="text-nowrap text-end">{{ $record->loans_count }}</td>
                <td class="text-nowrap text-end">{{ number_format($record->loans_sum_facility__amount__granted) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th class="text-start">Totals</th>
            <th class="text-end">{{ $records->count() }}</th>
            <th class="text-end">{{ $records->sum('loans_count') }}</th>
            <th class="text-end">{{ number_format($records->sum('loans_sum_facility__amount__granted')) }}</th>
        </tr>
        </tfoot>
    </table>
    <x-print-footer/>
@endsection
