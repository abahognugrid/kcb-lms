@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loans - Details')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Loan Contract: <span class="text-info">{{ $loan->Status }}</span>
            </h5>

            <div class="d-flex justify-content-end">
                <div class="dropdown">
                    <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                        <li>
                            <a href="{{ route('loan-accounts.ledger', $loan) }}" class="dropdown-item">
                                <i class="bx bx-receipt"></i> &nbsp;View Ledger
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <button href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#confirmWriteOff"
                                class="dropdown-item" type="button" {{ $loan->canWriteOff() ? '' : 'disabled' }}><i
                                    class="bx bx-edit-alt"></i> &nbsp;Write Off</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <hr class="mb-0 mt-0">
        <div class="card-body">
            <h6>Loan Details</h6>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Loan Account</strong>
                    <div>
                        {{ $loan->Credit_Account_Reference }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Loan Product</strong>
                    <div>
                        {{ $loan->loan_product->Name }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Loan Amount</strong>
                    <div>
                        <x-money :value="$loan->Credit_Amount" />
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Facility Amount Granted</strong>
                    <div>
                        <x-money :value="$loan->Facility_Amount_Granted" />
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Interest Rate</strong>
                    <div>
                        {{ $loan->Annual_Interest_Rate_at_Disbursement }}%
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Interest Cycle</strong>
                    <div>
                        {{ $loan->loan_term?->Interest_Cycle }}
                    </div>
                </li>
                {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Interest Amount</strong>
                    <div>
                        <x-money :value="$loan->totalInterest()" />
                    </div>
                </li> --}}
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Maturity Date</strong>
                    <div>
                        {{ \Carbon\Carbon::parse($loan->Maturity_Date)->format('d M, Y') }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Number Of Payments</strong>
                    <div>
                        {{ $loan->Number_of_Payments }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Total Outstanding balance</strong>
                    <div>
                        <x-money :value="$loan->totalOutstandingBalance()" />
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Total Amount Overdue</strong>
                    <div>
                        <x-money :value="$loan->getAmountDue()" />
                    </div>
                </li>
            </ul>

            <h6 class="mt-4">Customer Details</h6>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Full Name</strong>
                    <div>
                        {{ $loan->customer->First_Name }} {{ $loan->customer->Last_Name }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Gender</strong>
                    <div>
                        {{ $loan->customer->Gender }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Date of Birth</strong>

                    <div>
                        {{ \Carbon\Carbon::parse($loan->customer->Date_of_Birth)->format('d M, Y') }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>ID Number</strong>
                    <div>
                        {{ $loan->customer->ID_Number }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Telephone</strong>
                    <div>
                        {{ $loan->customer->Telephone_Number ?? $loan->customer->Delinked_Phone_Number }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Is Delinked</strong>
                    <div>
                        {{ $loan->customer->Is_Delinked ? 'Yes' : 'No' }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Delinked At</strong>
                    <div>
                        {{ $loan->customer->Delinked_At ?? 'N/A' }}
                    </div>
                </li>
            </ul>

            <h6 class="mt-4">Application Details</h6>
            <ul class="list-group">
                {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Application Reference</strong>
                    <div>
                        {{ $loan->loan_application->Credit_Application_Reference }}
    </div>
    </li> --}}
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Application Date</strong>

                    <div>
                        {{ \Carbon\Carbon::parse($loan->loan_application->Credit_Application_Date)->format('d M, Y') }}
                    </div>
                </li>
                {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Application Status</strong>
                    <div>
                        {{ $loan->loan_application->Credit_Application_Status }}
</div>
</li> --}}
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Purpose of Loan</strong>
                    <div>
                        {{ $loan->loan_application->Loan_Purpose }}
                    </div>
                </li>
            </ul>
        </div>
    </div>

    @if ($loan->Credit_Application_Status != 'Rejected')
        <livewire:loan-schedule :loan="$loan" />
    @endif

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Loan Repayments + (Fees & Penalties)
            </h5>
        </div>
        <hr class="mb-0 mt-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Transaction Date</th>
                            <th class="text-end">Principal</th>
                            <th class="text-end">Interest</th>
                            <th class="text-end">Penalties</th>
                            <th class="text-end">Fees</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($repayments as $repayment)
                            <tr>
                                <td class="text">
                                    {{ \Carbon\Carbon::parse($repayment->transaction_date)->format('d M, Y') }}
                                </td>
                                <td class="text-end">{{ number_format($repayment->principal_paid) }}</td>
                                <td class="text-end">{{ number_format($repayment->interest_paid) }}</td>
                                <td class="text-end">{{ number_format($repayment->penalties_paid) }}</td>
                                <td class="text-end">{{ number_format($repayment->fees_paid) }}</td>
                                <td class="text-end">
                                    {{ number_format(
                                        $repayment->principal_paid + $repayment->interest_paid + $repayment->penalties_paid + $repayment->fees_paid,
                                    ) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No loan repayment made yet.</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td><strong>Total</strong></td>
                            <td colspan="4"></td>
                            <td class="text-end">
                                @php
                                    $principalPaid = $loan->totalPrincipalPaid();
                                    $interestPaid = $loan->totalInterestPaid();
                                    $feesPaid = $loan->totalFeesPaid();
                                    $penaltiesPaid = $loan->totalPenaltiesPaid();
                                    $total = $principalPaid + $interestPaid + $feesPaid + $penaltiesPaid;
                                @endphp
                                <strong>
                                    {{ number_format($total) }}
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Loan Fees <span class="text-muted">(if any)</span>
            </h5>
        </div>
        <div class="card-body">
            @if ($loan->fees->count() == 0)
                <p>No loan fees</p>
            @endif
            @foreach ($loan->fees as $fee)
                <ul class="list-group mb-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Name</strong>
                        <div>
                            {{ $fee->loan_product_fee->Name }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Amount</strong>
                        <div>
                            <x-money :value="$fee->Amount_To_Pay" />
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Applicable At</strong>
                        <div>
                            {{ $fee->Charge_At }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Status</strong>
                        <div>
                            {{ $fee->Status }}
                        </div>
                    </li>
                </ul>
            @endforeach
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Loan Penalties <span class="text-muted">(if any)</span>
            </h5>
        </div>
        <hr class="mb-0 mt-0">
        <div class="card-body">
            @if ($loan->penalties->count() == 0)
                <p>No loan penalties</p>
            @endif
            @foreach ($loan->penalties as $fee)
                <ul class="list-group mb-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Amount To Pay</strong>
                        <div>
                            <x-money :value="$fee->Amount_To_Pay" />
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Amount Paid</strong>
                        <div>
                            <x-money :value="$fee->Amount" />
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Payment Status</strong>
                        <div>
                            {{ $fee->Status }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Date</strong>
                        <div>
                            {{ $fee->date }}
                        </div>
                    </li>
                </ul>
            @endforeach
        </div>
    </div>

    <div>
        <!-- Disable Modal -->
        <div class="modal fade" id="confirmWriteOff" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmWriteOffTitle">Confirm action
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to Write-Off this Loan?</p>
                        <span class="text-danger">This action cannot be undone.</span>
                        <div class="mt-4">
                            <label class="form-label">Write-off Date</label>
                            <input type="date" class="form-control" name="write_off_date" required
                                value="{{ date('Y-m-d') }}" form="write-off-form">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('loan-accounts.writeOff', $loan->id) }}" method="POST"
                            id="write-off-form">
                            @csrf
                            @method('PUT')
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            &nbsp;&nbsp;
                            <button type="submit" class="btn btn-danger">Confirm Action</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
