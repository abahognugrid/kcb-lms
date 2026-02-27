@extends('layouts/contentNavbarLayout')

@section('title', 'Ticket Reports')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4">Ticket Reports</h4>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <a href="{{ route('tickets.reports.export.pdf', request()->query()) }}" class="btn btn-danger w-100">
                    <i class="bx bx-download"></i> Export PDF
                </a> &nbsp;
                <a href="{{ route('tickets.reports.export.excel', request()->query()) }}" class="btn btn-success w-100">
                    <i class="bx bx-file"></i> Export Excel
                </a>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>User</th>
                            <th>Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $i => $ticket)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $ticket->title }}</td>
                                <td>{{ ucfirst($ticket->status) }}</td>
                                <td>{{ ucfirst($ticket->priority) }}</td>
                                <td>{{ $ticket->created_at->format('Y-m-d') }}</td>
                                <td>{{ $ticket->user->name ?? '-' }}</td>
                                <td>{{ $ticket->agent->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
