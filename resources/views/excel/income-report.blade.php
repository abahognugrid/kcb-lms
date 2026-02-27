<table>
    <thead>
        <tr>
            <th colspan="2" style="font-size: 20px; text-align: center;">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Income Report</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold; font-size: 12px; text-align: center; padding-top: 2px;">Period: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th style="font-weight: bold; width: 100px;">Account</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
            <tr>
                <td>{{ $record->name }}</td>
                <td style="text-align: right">{{ number_format($record->amount) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
