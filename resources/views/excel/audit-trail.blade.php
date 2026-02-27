<table>
    <thead>
        <tr>
            <th colspan="8" style="font-size: 18px; font-weight: bold; text-align: center;">{{ data_get($filters, 'partnerName') }}</th>
        </tr>
        <tr>
            <th colspan="8" style="font-size: 14px; font-weight: bold; text-align: center;">Audit Trail Report</th>
        </tr>
        <tr>
            <th colspan="8" style="font-size: 12px; text-align: center;">Period: {{ data_get($filters, 'startDate') }} to {{ data_get($filters, 'endDate') }}</th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: left; width: 60px;">ID</th>
            <th style="font-weight: bold; text-align: left; width: 150px;">User</th>
            <th style="font-weight: bold; text-align: left; width: 100px;">Action</th>
            <th style="font-weight: bold; text-align: left; width: 150px;">Performed On</th>
            <th style="font-weight: bold; text-align: left; width: 300px;">URL</th>
            <th style="font-weight: bold; text-align: left; width: 120px;">IP Address</th>
            <th style="font-weight: bold; text-align: left; width: 200px;">User Agent</th>
            <th style="font-weight: bold; text-align: left; width: 150px;">Performed At</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->user?->name ?? 'N/A' }}</td>
                <td>{{ ucfirst($record->event) }}</td>
                <td>{{ class_basename($record->auditable_type) }}</td>
                <td>{{ $record->url }}</td>
                <td>{{ $record->ip_address }}</td>
                <td>{{ $record->user_agent }}</td>
                <td>{{ $record->created_at->toDateTimeString() }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align: center;">No audit records found</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th style="font-weight: bold;">Total Records:</th>
            <th style="font-weight: bold;">{{ $records->count() }}</th>
            <th colspan="6"></th>
        </tr>
    </tfoot>
</table>
