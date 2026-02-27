<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="4" style="font-weight: bold; font-size: 18px; text-align: center">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="4" style="font-weight: bold; font-size: 16px; text-align: center">General Ledger Summary</th>
        </tr>
        <tr>
            <th colspan="4" style="font-size: 12px; text-align: center">From {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th colspan="4"></th>
        </tr>
        <tr class="table-header">
            <th style="font-weight: bold; text-align: left; width: 200px">Account Name</th>
            <th style="font-weight: bold; text-align: right; width: 100px">Total Debit</th>
            <th style="font-weight: bold; text-align: right; width: 100px">Total Credit</th>
            <th style="font-weight: bold; text-align: right; width: 100px">Balance</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($records as $entry)
        <tr>
            <td>{{ $entry->name }}</td>
            <td style="text-align: right;">{{ $entry->total_debit }}</td>
            <td style="text-align: right;">{{ $entry->total_credit }}</td>
            <td style="text-align: right;">
                @php
                    $balance = 0;
                    if (in_array($entry->type_letter, ['A', 'E'])) {
                        $balance = $entry->total_debit - $entry->total_credit;
                    } elseif (in_array($entry->type_letter, ['C', 'I', 'L'])) {
                        $balance = $entry->total_credit - $entry->total_debit;
                    }
                @endphp
                {{ $balance }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-print-footer/>
