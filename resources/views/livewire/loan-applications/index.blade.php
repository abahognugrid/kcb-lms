<div>
    <div class="card mb-4 mt-4">
        <div class="card-body">
            <div class="row align-items-center">
                {{-- <h5 class="mb-0">Loan Application</h5> --}}
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" wire:model.debounce.500ms="searchTerm" wire:keydown.enter="$refresh"
                            class="form-control rounded" placeholder="Type to search..." aria-label="Search" />
                        <div class="input-group-append ms-1">
                            <button class="btn btn-outline-secondary btn-lg" wire:click="$refresh" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Customer Name</th>
                        <th class="text-end">Phone Number</th>
                        <th>Loan Product</th>
                        <th class="text-end">Amount Applied</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th class="text-end">Application Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($records as $loanApplication)
                        <tr>
                            <td>{{ $loanApplication->application_number }}
                            <td>{{ $loanApplication->customer->name }}</td>
                            <td class="text-end">{{ $loanApplication->customer->Telephone_Number }}</td>
                            <td>{{ $loanApplication->loan_product->Name }}</td>
                            <td class="text-end">{{ $loanApplication->Amount }}</td>
                            <td>{{ $loanApplication->Loan_Purpose }}</td>
                            <td>{{ $loanApplication->Credit_Application_Status }}</td>
                            <td class="text-end">
                                {{ \Carbon\Carbon::parse($loanApplication->Credit_Application_Date)->format('Y-m-d') }}
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">

                                        <a class="dropdown-item"
                                            href="{{ route('loan-applications.show', $loanApplication->id) }}">
                                            <i class="bx bx-cog me-1"></i> Manage
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
