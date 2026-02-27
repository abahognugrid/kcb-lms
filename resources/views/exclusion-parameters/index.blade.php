@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Exclusion Parameters')
@section('content')
<div class="row">
    <div class="col mb-6 order-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Exclusion Parameters</h5>
                <span>
                    <a class="btn btn-primary" href="{{ route('exclusion-parameter.create') }}">Create</a>
                </span>
            </div>
            <div class="table-responsive text-nowrap p-5">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Parameter</th>
                            <th>Model</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($exclusionParameters as $exclusionParameter)
                        <tr>
                            <td>{{ $exclusionParameter->Name }}</td>
                            <td>{{ $exclusionParameter->Parameter }}</td>
                            <td>{{ $exclusionParameter->Model }}</td>
                            <td>{{ $exclusionParameter->Type }}</td>

                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('exclusion-parameter.edit', $exclusionParameter->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteModal{{ $exclusionParameter->id }}">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <div>
                            <!-- Modal -->
                            <div class="modal fade" id="confirmDeleteModal{{ $exclusionParameter->id }}"
                                data-bs-backdrop="static" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm Deletion
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this loan product type? <br> This action can
                                            not be undone.
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('exclusion-parameter.destroy', $exclusionParameter->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                &nbsp;&nbsp;
                                                <button type="submit" class="btn btn-danger">Confirm
                                                    deletion</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection