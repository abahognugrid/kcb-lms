@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Tickets Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <table id="report-table" class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th>#</th>
                <th>Title</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Resolved</th>
                <th>Locked</th>
                <th>Created</th>
                <th>User</th>
                <th>Agent</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tickets as $i => $ticket)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ Str::limit($ticket->title, 40) }}</td>
                    <td>{{ ucfirst($ticket->status) }}</td>
                    <td>{{ ucfirst($ticket->priority) }}</td>
                    <td>{{ $ticket->is_resolved ? 'Yes' : 'No' }}</td>
                    <td>{{ $ticket->is_locked ? 'Yes' : 'No' }}</td>
                    <td>{{ $ticket->created_at->format('Y-m-d') }}</td>
                    <td>{{ $ticket->user->name ?? '-' }}</td>
                    <td>{{ $ticket->agent->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start">Totals</th>
                <th class="text-start">{{ $tickets->count() }}</th>
                <th colspan="7"></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
