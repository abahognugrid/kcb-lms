@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-user')
@section('title', 'Ticket Details')

@section('content')
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $ticket->title }}</h4>
                            <small class="text-muted">Created {{ $ticket->created_at->diffForHumans() }} by
                                {{ $ticket->user->name }}</small>
                        </div>
                        @if (!auth()->user()->partner_id)
                            <span
                                class="badge bg-{{ ['low' => 'info', 'medium' => 'warning', 'high' => 'danger'][$ticket->priority ?? 'unspecified'] ?? 'secondary' }} px-3 py-2">
                                {{ ucfirst($ticket->priority ?? 'unspecified') }}
                            </span>
                        @endif

                    </div>
                    <div class="card-body">
                        @if ($ticket->status !== 'closed')

                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted">Assigned Agent</h6>
                                @if ($ticket->assigned_to)
                                    <span class="badge bg-warning text-dark px-3 py-2">
                                        <i class="fas fa-user-check me-1"></i>{{ $ticket->agent->name }}
                                        ({{ $ticket->agent->email }})
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="fas fa-user-times me-1"></i> Unassigned
                                    </span>
                                @endif
                            </div>
                        @endif
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted">Message</h6>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0">{{ $ticket->message }}</p>
                            </div>
                        </div>

                        {{-- <div class="mb-4">
                            <h6 class="text-uppercase text-muted">Tags</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($ticket->categories as $category)
                                    <span class="badge bg-primary">{{ $category->name }}</span>
                                @endforeach
                                @foreach ($ticket->labels as $label)
                                    <span class="badge bg-secondary">{{ $label->name }}</span>
                                @endforeach
                            </div>
                        </div> --}}
                        @if ($ticket->status !== 'closed')
                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted">Add Comment</h6>
                                <form action="{{ route('tickets.comment', $ticket) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control" name="message" rows="3" placeholder="Write a comment..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Post Comment</button>
                                </form>
                            </div>

                            <hr>
                        @endif
                        <div>
                            <h6 class="text-uppercase text-muted mb-3">Comments ({{ $ticket->messages->count() }})</h6>
                            @forelse ($ticket->messages as $comment)
                                <div class="border rounded p-3 mb-3 bg-white">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong>{{ $comment->user->name }}</strong>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0">{{ $comment->message }}</p>
                                </div>
                            @empty
                                <p class="text-muted">No comments yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="card-footer d-flex flex-wrap justify-content-between align-items-center bg-white">
                        <span
                            class="badge bg-{{ [
                                'open' => 'primary',
                                'in_progress' => 'info',
                                'resolved' => 'success',
                                'closed' => 'secondary',
                            ][$ticket->status] }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>

                        @if (!auth()->user()->partner_id)
                            <div class="btn-group mt-2 mt-md-0">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#statusModal">
                                    <i class="fas fa-sync-alt me-1"></i>Change Status
                                </button>
                                @if (!$ticket->assigned_to)
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#assignModal">
                                        <i class="fas fa-user-plus me-1"></i>Assign Agent
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Modal --}}
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('tickets.status', $ticket) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Ticket Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="status" class="form-select" required>
                        @foreach (['open', 'in_progress', 'resolved', 'closed'] as $status)
                            <option value="{{ $status }}" {{ $ticket->status === $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> &nbsp;
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Assign Modal --}}
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('tickets.assign', $ticket) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assign Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">

                        <select name="agent_id" class="form-select" required>
                            <option value="">-- Select Agent --</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}"
                                    {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }} ({{ $agent->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority Field -->
                    <div class="mb-3">
                        <label for="modal_priority" class="form-label">Priority</label>
                        <select class="form-select @error('priority') is-invalid @enderror" id="modal_priority"
                            name="priority" required>
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> &nbsp;
                    <button class="btn btn-primary" type="submit">Assign</button>
                </div>
            </form>
        </div>
    </div>
@endsection
