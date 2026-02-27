@extends('layouts/contentNavbarLayout')
@section('title', 'User - Edit')
@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="mb-0">Edit User</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-4">
                        <label for="partner">Partner</label>
                        <input type="text" name="partner" class="form-control"
                            value="{{ $user->partner->Institution_Name }}" required readonly>
                    </div>
                    <div class="form-group mb-4">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-4">
                        <label for="role" class="form-label">Role Name</label>
                        <select class="form-select" id="type" name="role_id" required>
                            <option value="">Choose...</option>
                            <?php foreach ($roles as $role): ?>
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <input type="hidden" name="partner_id" value="{{ $user->partner->id }}">

                    <button type="submit" class="btn btn-dark">Update</button>
                </form>
            </div>
        </div>

        <div>
            <!-- Modal -->
            <div class="modal fade" id="confirmDeleteModal" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm Deletion
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this user?
                        </div>
                        <div class="modal-footer">
                            <form action="/users/{{ $user->id }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                                &nbsp;&nbsp;
                                <button type="submit" class="btn btn-danger">
                                    Confirm deletion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
