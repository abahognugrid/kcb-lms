@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Partners')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Partners</h5>
                    <?php if(Auth::user()->is_admin): ?>
                    <span>
                        <a class="btn btn-dark" href="{{ route('partner.create') }}">Create New Partner</a>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="table-responsive text-nowrap p-5">
                    <table class="table">

                        <thead>
                            <tr>
                                <th>Institution Name</th>
                                <th>Identification Code</th>
                                <th>Institution Type</th>
                                <th>Telephone Number</th>
                                <th>Email Address</th>
                                {{-- <th>License Number</th> --}}
                                <th>Access Type</th>
                                <?php if(Auth::user()->is_admin): ?>
                                <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($partners as $partner)
                                <tr>
                                    <td>{{ $partner->Institution_Name }}</td>
                                    <td>{{ $partner->Identification_Code }}</td>
                                    <td>{{ $partner->Institution_Type }}</td>
                                    <td>{{ $partner->Telephone_Number }}</td>
                                    <td>{{ $partner->Email_Address }}</td>
                                    {{-- <td>{{ $partner->License_Number }}</td> --}}
                                    <td>{{ $partner->Access_Type }}</td>
                                    <?php if(Auth::user()->is_admin): ?>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('partner.edit', $partner->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal"
                                                    data-bs-target="#confirmDeleteModal{{ $partner->id }}">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <div>
                                    <!-- Modal -->
                                    <div class="modal fade" id="confirmDeleteModal{{ $partner->id }}"
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
                                                    Are you sure you want to delete this Partner? <br> This action can
                                                    not be undone.
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('partner.destroy', $partner->id) }}"
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
                </div>
            </div>
        </div>
    </div>
@endsection
