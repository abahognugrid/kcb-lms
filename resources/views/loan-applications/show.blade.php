@extends('layouts.contentNavbarLayout')
@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loan Application - Appraisal')
@section('content')

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="">Customer Details</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Full Name:</strong>
                            <div>
                                {{ $application->customer->name }}
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Gender:</strong>
                            <div>
                                {{ $application->customer->Gender }}
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Date of Birth:</strong>

                            <div>{{ $application->customer->Date_of_Birth }}</div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>ID Number:</strong>
                            <div>
                                {{ $application->customer->ID_Number }}
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Telephone:</strong>
                            <div>
                                {{ $application->customer->Telephone_Number }}
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Application Details</h5>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Credit Application Date</strong>
                            <span>{{ $application->Credit_Application_Date->toDateString() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Amount</strong>
                            <span>{{ number_format($application->Amount) }}
                                <small>{{ $application->Currency }}</small></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Status</strong>
                            <span>{{ $application->Credit_Application_Status }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- check if customer hasCreditScore -->
    @if ($application->creditScore)
        <div class="card mt-4">
            <div class="card-body">
                <h5>CRB Details</h5>
                <!-- draw a table of all customer credit scores below -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>MNO Months Active</th>
                            <th>MNO Band</th>
                            <th>MNO Score</th>
                            <th>MNO Rating</th>
                            <th>MNO 6 Months Turnover </th>
                            <th>MNO Active Loans </th>
                            <th>MNO Rating </th>
                            <th>CRB Active Loans </th>
                            <th>CRB Rating </th>
                            <th>Other Active Loans </th>
                            <th>Other Provider Rating</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $application->creditScore->mnoMonthsActive }}</td>
                            <td>{{ $application->creditScore->mnoBand }}</td>
                            <td>{{ $application->creditScore->mnoScore }}</td>
                            <td>{{ $application->creditScore->mnoRating }}</td>
                            <td>{{ number_format($application->creditScore->mnoMonthlyTurnoverAmount6Months) }}</td>
                            <td>{{ $application->creditScore->mnoAccounts12Months }}</td>
                            <!-- change this line later to open accounts -->
                            <td>{{ $application->creditScore->mnoRating }}</td>
                            <td>{{ $application->creditScore->crbOpenAccounts }}</td>
                            <td>{{ $application->creditScore->crbRating }}</td>
                            <td>{{ $application->creditScore->altOpenAccounts }}</td>
                            <td>{{ $application->creditScore->altRating }}</td>
                            <td>{{ $application->creditScore->created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mt-4">Loan Details</h5>

            @if (!$application->loan)
                <p>No loan details</p>
            @else
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Loan Duration:</strong>
                        <div>
                            {{ $application->loan->Term }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Daily Repayment Amount:</strong>
                        <div>
                            {{ $application->loan->getDailyRepayment() }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Total Repayment Amount:</strong>
                        <div>
                            {{ $application->loan->totalToBePaid() }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Total Fees</strong>
                        <div>
                            {{ $application->loan->totalFees() }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Repayment Frequency</strong>
                        <div>
                            {{ $application->loan->Credit_Payment_Frequency }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Annual Interest Rate:</strong>
                        <div>
                            {{ $application->loan->Interest_Rate }}
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Interest Calculation Method</strong>
                        <div>
                            {{ $application->loan->Interest_Calculation_Method }}
                        </div>
                    </li>
                </ul>
            @endif
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Loan Fees <span class="text-muted"></span>
            </h5>
        </div>
        <div class="card-body">
            @if ($application->loan && $application->loan->fees->isEmpty())
                <p>No loan fees</p>
            @elseif ($application->loan && $application->loan->fees)
                @foreach ($application->loan->fees as $fee)
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Name:</strong>
                            <div>
                                {{ $fee->loan_product_fee->Name }}
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Amount:</strong>
                            <div>
                                {{ $fee->Amount_To_Pay }}
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Applicable At:</strong>
                            <div>
                                {{ $fee->Charge_At }}
                            </div>
                        </li>
                    </ul>
                @endforeach
            @endif

        </div>
    </div>
@endsection
