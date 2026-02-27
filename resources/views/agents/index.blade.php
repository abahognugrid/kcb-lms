@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-user')
@section('title', 'Tickets')
@section('content')
    <div class="container">
        <h3>Agents</h3>

        <div class="card">
            <div class="card-body">
                <!-- Add Agent Button (triggers modal) -->
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createAgentModal">
                    <i class="fas fa-plus"></i> Add New Agent
                </button>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Assigned Tickets</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agents as $agent)
                            <tr>
                                <td>{{ $agent->name }}</td>
                                <td>{{ $agent->email }}</td>
                                <td>

                                    <span class="badge bg-success" title="Open Tickets">
                                        {{ $agent->open_tickets_count ?? 0 }}
                                    </span>
                                    <span class="badge bg-info" title="In Progress">
                                        {{ $agent->in_progress_tickets_count ?? 0 }}
                                    </span>
                                    <a href="{{ route('agents.show', $agent) }}" class="badge bg-secondary"
                                        title="Total Tickets">
                                        {{ $agent->tickets_count ?? 0 }}
                                    </a>
                                </td>
                                <td>
                                    <!-- Edit Button (triggers modal) -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editAgentModal" data-agent-id="{{ $agent->id }}"
                                        data-agent-name="{{ $agent->name }}" data-agent-email="{{ $agent->email }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('agents.destroy', $agent) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $agents->links() }}
            </div>
        </div>
    </div>

    <!-- Create Agent Modal -->
    <div class="modal fade" id="createAgentModal" tabindex="-1" aria-labelledby="createAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('agents.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAgentModalLabel">Add New Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> &nbsp;
                        <button type="submit" class="btn btn-primary">Save Agent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Agent Modal -->
    <div class="modal fade" id="editAgentModal" tabindex="-1" aria-labelledby="editAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editAgentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAgentModalLabel">Edit Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> &nbsp;
                        <button type="submit" class="btn btn-primary">Update Agent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle edit modal -->
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // When edit button is clicked
            const editAgentModal = document.getElementById('editAgentModal');
            editAgentModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const agentId = button.getAttribute('data-agent-id');
                const agentName = button.getAttribute('data-agent-name');
                const agentEmail = button.getAttribute('data-agent-email');

                // Update the modal's content
                document.getElementById('edit_name').value = agentName;
                document.getElementById('edit_email').value = agentEmail;

                // Set the form action
                document.getElementById('editAgentForm').action = `/agents/${agentId}`;
            });
        });
    </script>
@endsection
@endsection
