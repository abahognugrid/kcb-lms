@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loan Products')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Loan Products</h5>
                    @if (Auth::user()->can('update loan-products'))
                        <div class="row">
                            <div class="col-md-7">
                                <form method="GET" action="{{ route('loan-products.index') }}" class="mb-4">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            class="form-control" placeholder="Search..." aria-label="Search" />
                                        <button type="submit" class="btn btn-outline-dark">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                <circle cx="11" cy="11" r="8" />
                                                <path d="m21 21-4.3-4.3" />
                                            </svg>
                                            <span>Search</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @if (Auth::user()->is_admin)
                                <div class="col-md-5 text-end">
                                    <a class="btn btn-dark" href="{{ route('loan-products.create') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                        <span>Create Loan Product</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="table-responsive text-nowrap p-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Product Type</th>
                                <th>Partner</th>
                                @if (Auth::user()->can('update loan-products'))
                                    <th class="text-end">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($loan_products as $loan_product)
                                <tr>
                                    <td>{{ $loan_product->Name }}</td>
                                    <td>{{ $loan_product->Code }}</td>
                                    <td>{{ $loan_product->loan_product_type?->Name }}</td>
                                    <td>{{ $loan_product->partner?->Institution_Name }}</td>
                                    @if (Auth::user()->can('update loan-products'))
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if (Auth::user()->is_admin)
                                                        <a class="dropdown-item"
                                                            href="{{ route('loan-products.edit', $loan_product->id) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                    @endif
                                                    <a class="dropdown-item"
                                                        href="{{ route('loan-products.show', $loan_product->id) }}">
                                                        <i class="bx bx-cog me-1"></i> Manage
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                <div>
                                    <!-- Modal -->
                                    <div class="modal fade" id="confirmDeleteModal{{ $loan_product->id }}"
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
                                                    Are you sure you want to delete this loan product type? <br> This action
                                                    can
                                                    not be undone.
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('loan-products.destroy', $loan_product->id) }}"
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
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination">
                        {{ $loan_products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
