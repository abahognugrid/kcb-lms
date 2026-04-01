@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Customers')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Customers</h5>
                        <form method="GET" action="{{ route('customers.index') }}" class="mt-2">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Search..." aria-label="Search" />
                                <button type="submit" class="btn btn-info">Search</button>
                            </div>
                        </form>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#bulkDelinkModal">
                                    Bulk Delink Customers
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Bulk Delink Modal -->
                    <div class="modal fade" id="bulkDelinkModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Bulk Delink Customers</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('customers.bulk-delink') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="file" class="form-label">Upload CSV/Excel File</label>
                                            <input type="file" class="form-control" id="file" name="file"
                                                accept=".csv,.xlsx" required>
                                            <small class="text-muted">Accepted formats: CSV, Excel</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import & Delink</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap p-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th class="text-end">Telephone Number</th>
                                <th>Email Address</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->First_Name }}</td>
                                    <td>{{ $customer->Last_Name }}</td>
                                    <td>{{ $customer->Gender }}</td>
                                    <td class="text-end">{{ $customer->Telephone_Number }}</td>
                                    <td>{{ $customer->Email_Address }}</td>
                                    <td>{{ $customer->created_at }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('customer.show', $customer->id) }}"
                                                class="btn btn-sm btn-outline-warning">View Profile</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
