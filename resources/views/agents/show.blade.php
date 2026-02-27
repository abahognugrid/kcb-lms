@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-user')
@section('title', 'Tickets')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Agent: {{ $agent->name }}</h1>
            <a href="{{ route('agents.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Agents
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Agent Details</h5>
                        <p><strong>Name:</strong> {{ $agent->name }}</p>
                        <p><strong>Email:</strong> {{ $agent->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Assignment Stats</h5>
                        <p><strong>Total Tickets Assigned:</strong> {{ $tickets->total() }}</p>
                        <p><strong>Open Tickets:</strong> {{ $agent->tickets()->where('status', 'open')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Assigned Tickets</h5>
            </div>
            <div class="card-body">
                @if ($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td>#{{ $ticket->id }}</td>
                                        <td>{{ Str::limit($ticket->title, 40) }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ [
                                                    'open' => 'primary',
                                                    'in_progress' => 'info',
                                                    'resolved' => 'success',
                                                    'closed' => 'secondary',
                                                ][$ticket->status] }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ [
                                                    'low' => 'info',
                                                    'medium' => 'warning',
                                                    'high' => 'danger',
                                                ][$ticket->priority] }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $tickets->links() }}
                @else
                    <div class="alert alert-info">No tickets assigned to this agent.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
