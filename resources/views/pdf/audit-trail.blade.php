@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Audit Trail Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-start">ID</th>
                <th class="text-start">User</th>
                <th class="text-start">Action</th>
                <th class="text-start">Performed On</th>
                <th class="text-start">URL</th>
                <th class="text-start">IP Address</th>
                <th class="text-start">Performed At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->user?->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($record->event) }}</td>
                    <td>{{ class_basename($record->auditable_type) }}</td>
                    <td style="font-size: 8px; word-break: break-all;">{{ \Illuminate\Support\Str::limit($record->url, 50) }}</td>
                    <td>{{ $record->ip_address }}</td>
                    <td style="font-size: 8px;">{{ $record->created_at->format('d-m-Y H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No audit records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>Total Records</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th colspan="5"></th>
            </tr>
        </tfoot>
    </table>
@endsection
