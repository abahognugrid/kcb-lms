<table id="report-table" class="table table-bordered">
    <thead>
        <tr>
            <th colspan="9" style="font-size: 20px; text-align: center; padding: 2px;">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="9" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Ticket Report
            </th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 8px; text-align: center; padding: 2px;">
                From: {{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }} &nbsp;&nbsp; | &nbsp;&nbsp; To:
                {{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}
            </th>
        </tr>
        <tr>
            <th colspan="9"></th>
        </tr>
        <tr class="table-header">
            <th colspan="9"></th>
        </tr>
        <tr>
            <th><strong>#</strong></th>
            <th><strong>Title</strong></th>
            <th><strong>Status</strong></th>
            <th><strong>Priority</strong></th>
            <th><strong>Resolved</strong></th>
            <th><strong>Locked</strong></th>
            <th><strong>Created</strong></th>
            <th><strong>User</strong></th>
            <th><strong>Agent</strong></th>
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
            <th class="text-start"><strong>Totals</strong></th>
            <th class="text-start"><strong>{{ $tickets->count() }}</strong></th>
            <th colspan="7"></th>
        </tr>
    </tfoot>
</table>
<x-print-footer />
