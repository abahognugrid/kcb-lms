@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Income Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Account</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($records as $entry)
            <tr>
                <td>{{ $entry->name }}</td>
                <td class="text-end"><x-money :value="$entry->amount" /></td>
            </tr>
        @empty
            <tr>
                <td colspan="2">No records found</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <x-print-footer/>
@endsection
