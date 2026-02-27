<div>
    <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
</div>
@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'SMS Templates')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">SMS Templates</h5>
                    <div class="row">
                        <div class="col-md-7">
                            <form method="GET" action="{{ route('sms-templates.index') }}" class="mb-4">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Search..." aria-label="Search" />
                                    <button type="submit" class="btn btn-info">Search</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-5">
                            <a class="btn btn-dark" href="{{ route('sms-template.create') }}">Create New Template</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap p-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Template</th>
                                <th>Loan Product</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($templates as $template)
                                <tr>
                                    <td>{{ $template->Day }}</td>
                                    <td>{{ $template->Template }}</td>
                                    <td>{{ $template->loanProduct->Name }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                    href="{{ route('sms-template.edit', $template->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal"
                                                    data-bs-target="#confirmDeleteModal{{ $template->id }}">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="confirmDeleteModal{{ $template->id }}"
                                            data-bs-backdrop="static" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm
                                                            Deletion
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this record?
                                                        <br> This action can
                                                        not be undone.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form action="{{ route('sms-template.delete', $template->id) }}"
                                                            method="POST">
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination">
                        {{ $templates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
