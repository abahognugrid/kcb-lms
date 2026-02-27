@extends('layouts/contentNavbarLayout')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h3 class="mb-0 text-gray-800">Ticket Labels</h3>
            <a href="#" data-bs-toggle="modal" data-bs-target="#createCategoryModal"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus-circle fa-sm text-white-50"></i> Add Label
            </a>
        </div>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Visibility</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($labels as $label)
                                <tr>
                                    <td>{{ $label->name }}</td>
                                    <td>{{ $label->slug }}</td>
                                    <td>
                                        <span class="badge bg-{{ $label->is_visible ? 'success' : 'secondary' }}">
                                            {{ $label->is_visible ? 'Visible' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td>{{ $label->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal-{{ $label->id }}">
                                            Edit
                                        </button>
                                        <div class="modal fade" id="editCategoryModal-{{ $label->id }}" tabindex="-1"
                                            aria-hidden="false" aria-modal="true" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Label</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('labels.update', $label) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="name-{{ $label->id }}"
                                                                    class="form-label">Label Name</label>
                                                                <input type="text" class="form-control"
                                                                    id="name-{{ $label->id }}" name="name"
                                                                    value="{{ $label->name }}" required>
                                                            </div>
                                                            <div class="mb-3 form-check">
                                                                <input type="hidden" name="is_visible" value="0">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="visible-{{ $label->id }}" name="is_visible"
                                                                    value="1"
                                                                    {{ $label->is_visible ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="visible-{{ $label->id }}">Visible to
                                                                    users</label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button> &nbsp;
                                                            <button type="submit" class="btn btn-primary">Update
                                                                Label</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Form -->
                                        <form action="{{ route('labels.destroy', $label) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $labels->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Label Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Label</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('labels.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Label Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="hidden" name="is_visible" value="0">
                            <input type="checkbox" class="form-check-input" id="is_visible" name="is_visible" value="1"
                                checked>
                            <label class="form-check-label" for="is_visible">Visible to users</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> &nbsp;
                        <button type="submit" class="btn btn-primary">Save Label</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
