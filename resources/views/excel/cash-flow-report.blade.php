@extends('excel.layouts')

@section('content')
    <table id="report-table" class="table table-bordered">
        <thead>
            <tr>
                <th colspan="3" style="font-size: 20px; text-align: center; padding: 2px;">{{ $partner->Institution_Name }}</th>
            </tr>
            <tr>
                <th colspan="3" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Cash Flow Statement</th>
            </tr>
            <tr>
                <th colspan="3" style="font-weight: bold; font-size: 14px; text-align: center; padding: 2px;">As at: {{ $filters['endDate'] }}</th>
            </tr>
            <tr>
                <th colspan="3"></th>
            </tr>
            <tr>
                <th style="font-weight: bold; border: 1px solid black; padding: 2px; background-color: #999999">Date</th>
                <th style="font-weight: bold; border: 1px solid black; padding: 2px; background-color: #999999">Transaction Type</th>
                <th style="font-weight: bold; border: 1px solid black; padding: 2px; background-color: #999999; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($journalEntries as $entry)
                <tr>
                    <td>{{ $entry->created_at->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($entry->cash_type ?? '-') }}</td>
                    <td style="text-align: right;">{{ number_format($entry->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="border: 1px solid black; padding: 2px; text-align: center;">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <x-print-footer/>
@endsection
