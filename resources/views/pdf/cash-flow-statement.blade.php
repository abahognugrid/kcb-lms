@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partner->Institution_Name }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Cash Flow Statement</h4>
        <p style="margin-top: 0; font-size: 10px">As at: {{ $filters['endDate'] }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <th>Date</th>
            <th>Transaction Type</th>
            <th class="text-end">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($entries as $entry)
            <tr>
                <td>{{ $entry->created_at->format('Y-m-d') }}</td>
                <td>{{ ucfirst($entry->cash_type ?? '-') }}</td>
                <td class="text-end"><x-money :value="$entry->amount" /></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <x-print-footer/>
@endsection
