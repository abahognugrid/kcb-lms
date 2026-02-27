@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-user')
@section('title', 'Tickets')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Tickets</h3>
            @if (!auth()->user()->is_admin)
                <a href="#" data-bs-toggle="modal" data-bs-target="#createTicketModal"
                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> New Ticket
                </a>
            @endif
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        @if (auth()->user()->is_admin)
                            <h5>All Tickets</h5>
                        @else
                            <h5>My Tickets</h5>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="float-end">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search..."
                                    value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>

                                @if (auth()->user()->is_admin)
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="">All Statuses</option>
                                        @foreach (['open', 'in_progress', 'resolved', 'closed'] as $status)
                                            <option value="{{ $status }}"
                                                {{ request('status') === $status ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="priority" class="form-select" onchange="this.form.submit()">
                                        <option value="">All Priorities</option>
                                        @foreach (['low', 'medium', 'high'] as $priority)
                                            <option value="{{ $priority }}"
                                                {{ request('priority') === $priority ? 'selected' : '' }}>
                                                {{ ucfirst($priority) }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                @if (auth()->user()->is_admin)
                                    <th>Reported By</th>
                                @endif
                                {{-- <th>Priority</th> --}}
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>#{{ $ticket->id }}</td>
                                    <td>{{ Str::limit($ticket->title, 40) }}</td>
                                    @if (auth()->user()->is_admin)
                                        <td>{{ $ticket->user->name }}</td>
                                    @endif
                                    {{-- <td>
                                        <span
                                            class="badge bg-{{ [
                                                'low' => 'info',
                                                'medium' => 'warning',
                                                'high' => 'danger',
                                            ][$ticket->priority] }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td> --}}
                                    <td>
                                        <span
                                            class="badge bg-{{ [
                                                'open' => 'info',
                                                'in_progress' => 'secondary',
                                                'resolved' => 'success',
                                                'closed' => 'dark',
                                            ][$ticket->status] }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($ticket->assigned_to)
                                            <b> {{ $ticket->agent?->name }}</b>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->is_admin ? 7 : 6 }}" class="text-center">
                                        No tickets found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Ticket Modal -->
    <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTicketModalLabel">Create New Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
                        id="ticketForm">
                        @csrf

                        <!-- Title Field -->
                        <div class="mb-3">
                            <label for="modal_title" class="form-label">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="modal_title"
                                name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Field -->
                        <div class="mb-3">
                            <label for="modal_message" class="form-label">Description</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="modal_message" name="message" rows="5"
                                required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Categories Field -->
                        {{-- <div class="mb-3">
                            <label class="form-label">Categories</label>
                            <div class="row">
                                @foreach ($categories as $category)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="modal_category_{{ $category->id }}" name="categories[]"
                                                value="{{ $category->id }}"
                                                {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal_category_{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('categories')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <!-- Labels Field -->
                        {{-- <div class="mb-3">
                            <label class="form-label">Labels</label>
                            <div class="row">
                                @foreach ($labels as $label)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="modal_label_{{ $label->id }}" name="labels[]"
                                                value="{{ $label->id }}"
                                                {{ in_array($label->id, old('labels', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal_label_{{ $label->id }}">
                                                {{ $label->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('labels')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div> --}}
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    &nbsp;
                    <button type="submit" class="btn btn-primary" form="ticketForm">
                        <i class="fas fa-paper-plane me-1"></i> Submit Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
