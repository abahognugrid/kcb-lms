@extends('layouts/blankLayout')

@section('title', 'Monthly loan summary')

@section('content')
<div class="container">
    <h1 class="mb-4">Monthly Loan Activity Summary</h1>

    <div class="card">
        <div class="card-body">
            <p>Dear Admin,</p>
            <p>Please find the below summary of your loan.</p>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Number of Loan Applications This Month:</strong> {{ $loan_summary['loanApplicationsCount'] }}
                </li>
                <li class="list-group-item">
                    <strong>Number of Loans Disbursed This Month:</strong> {{ $loan_summary['loansDisbursedCount'] }}
                </li>
                <li class="list-group-item">
                    <strong>Total Value of Loans Disbursed This Month:</strong> {{ $loan_summary['totalLoansDisbursed'] }}
                </li>
                <li class="list-group-item">
                    <strong>Loans That Fell Due This Month:</strong> {{ $loan_summary['loansDueThisMonthCount'] }}
                </li>
                <li class="list-group-item">
                    <strong>Total Amount That Fell Due This Month:</strong> {{ $loan_summary['totalDueThisMonth'] }}
                </li>
                <li class="list-group-item">
                    <strong>Total Amount Repaid This Month:</strong> {{ $loan_summary['totalRepaidThisMonth'] }}
                </li>
            </ul>
            <p>Thank you.</p>
            <h5>GnuGrid LMS Team</h5>
        </div>
    </div>
</div>
@endsection