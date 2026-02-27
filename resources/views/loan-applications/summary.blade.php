@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loan Application Summary')
@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Loan Application Summary
            </h5>
            <div class="button-group">
                <button class="btn btn-sm btn-primary">
                    <i class="bx bx-printer"></i> &nbsp;
                    Print
                </button>
            </div>
        </div>
        <hr class="mb-0 mt-0">
        <div class="card-body">
            <h5 class="mt-0">Customer Details</h5>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Full Name:</strong>
                    <div>
                        {{ $customer->First_Name }} {{ $customer->Last_Name }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Gender:</strong>
                    <div>
                        {{ $customer->Gender }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Date of Birth:</strong>

                    <div>
                        {{ \Carbon\Carbon::parse($customer->Date_of_Birth)->format('d M, Y') }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>ID Number:</strong>
                    <div>
                        {{ $customer->ID_Number }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Telephone:</strong>
                    <div>
                        {{ $customer->Telephone_Number }}
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mt-4">Application Details</h5>

            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Application Date:</strong>

                    <div>
                        {{ \Carbon\Carbon::parse($loanRecordDetails['Credit_Application_Date'])->format('d M, Y') }}
                    </div>
                </li>
                {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Application Reference:</strong>
                    <div>
                        {{ $applications->loan_application->Credit_Application_Reference }}
    </div>
    </li> --}}
                {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Application Status:</strong>
                    <!-- <div>
                        {{ $applications->loan_application->Credit_Application_Status }}
</div> -->
</li> --}}
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Amount Requested:</strong>
                    <div>
                        {{ $loanRecordDetails['Amount'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Purpose of Loan:</strong>
                    <div>
                        {{ $loanRecordDetails['Loan_Purpose'] }}
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="">Loan Details</h5>

            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Loan Duration:</strong>
                    <div>
                        {{ $loanData['loanDuration'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Repayment Amount</strong>
                    <div>
                        {{ $loanData['repaymentAmount'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Total RepaymentAmount</strong>
                    <div>
                        {{ $loanData['totalRepaymentAmount'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Pay Off</strong>
                    <div>
                        {{ $loanData['payOff'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Facilitation Fee</strong>
                    <div>
                        {{ $loanData['facilitationFee'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Repayment Frequency</strong>
                    <div>
                        {{ $loanData['frequencyOfInstallmentRepayment'] }}
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="">Interest Details</h5>

            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Annual Interest Rate:</strong>
                    <div>
                        {{ $loanData['interestRate'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Interest Amount</strong>
                    <div>
                        {{ $loanData['totalInterest'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Interest Cycle</strong>
                    <div>
                        {{ $loanData['interestCycle'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Repayment Frequency</strong>
                    <div>
                        {{ $loanData['frequencyOfInstallmentRepayment'] }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Maturity Date:</strong>
                    <div>
                        {{ \Carbon\Carbon::parse($loanData['Maturity_Date'])->format('d M, Y') }}
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Number of Payments:</strong>
                    <div>
                        {{ $loanData['numberOfPayments'] }}
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="">
                Loan Fees <span class="text-muted"></span>
            </h5>

            @if (empty($loanData['feesStructure']))
                <p>No loan fees</p>
            @endif
            @foreach ($loanData['feesStructure'] as $fee)
                <ul class="list-group mb-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Name:</strong>
                        <div>
                            {{ $fee['name'] }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Amount:</strong>
                        <div>
                            {{ $fee['amount'] }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Charged At:</strong>
                        <div>
                            {{ $fee['charged_at'] }}
                        </div>
                    </li>
                </ul>
            @endforeach
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <ul>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <p></p>
                    <div class="btn-group">
                        <a class="btn btn-sm btn-outline-secondary" href="javascript:void(0);" data-bs-toggle="modal"
                            data-bs-target="#confirmLoanApplicationModal">
                            <span class="text-primary">
                                {{-- <i class="bx bx-trash"></i> &nbsp; --}}
                                Confirm
                            </span>
                        </a>
                        <a class="btn btn-sm btn-outline-secondary" href="javascript:void(0);" data-bs-toggle="modal"
                            data-bs-target="#cancelLoanApplicationModal">
                            <span class="text-danger">
                                {{-- <i class="bx bx-edit"></i> &nbsp; --}}
                                Cancel
                            </span>
                        </a>
                    </div>
                </li>
            </ul>
            @include('loan-applications.partials.confirm-loan-application-modal')
            @include('loan-applications.partials.cancel-loan-application-modal')
        </div>
    </div>
@endsection
