@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Business Rules')
@section('content')
<div class="row">
    <div class="col mb-6 order-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Business Rules</h5>
                <div class="row">
                    <div class="col-md-7">
                        <form method="GET" action="{{ route('business-rules.index') }}" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search..." aria-label="Search" />
                                <button type="submit" class="btn btn-info">Search</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5">
                        <a class="btn btn-dark" href="{{ route('business-rule.create') }}">Create New Rule</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap p-5">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Partner</th>
                            <th>Parameter</th>
                            <th>Option</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($businessRules as $businessRule)
                        <tr>
                            <td>{{ $businessRule->partner->Institution_Name }}</td>
                            <td>{{ $businessRule->parameter->Name }}</td>
                            <td>{{ $businessRule->Option }}</td>
                            <td>{{ $businessRule->Value }}</td>

                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('business-rule.edit', $businessRule->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteModal{{ $businessRule->id }}">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Modal -->
                        <div class="modal fade" id="confirmDeleteModal{{ $businessRule->id }}"
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
                                        <form action="{{ route('business-rule.destroy', $businessRule->id) }}" method="POST">
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
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination">
                    {{ $businessRules->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

