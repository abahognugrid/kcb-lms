@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">General Ledger Summary Report</h4>
        <p style="margin-top: 0; font-size: 10px">From {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <th class="text-start">Account Name</th>
            <th class="text-end">Total Debit</th>
            <th class="text-end">Total Credit</th>
            <th class="text-end">Balance</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($records as $entry)
            <tr>
                <td>{{ $entry->name }}</td>
                <td class="text-end"><x-money :value="$entry->total_debit" /></td>
                <td class="text-end"><x-money :value="$entry->total_credit" /></td>
                <td class="text-end">
                    @php
                        $balance = 0;
                        if (in_array($entry->type_letter, ['A', 'E'])) {
                            $balance = $entry->total_debit - $entry->total_credit;
                        } elseif (in_array($entry->type_letter, ['C', 'I', 'L'])) {
                            $balance = $entry->total_credit - $entry->total_debit;
                        }
                    @endphp
                    <x-money :value="$balance" />
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <x-print-footer/>
@endsection
