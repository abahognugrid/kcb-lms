@extends('layouts/blankLayout')

@section('title', 'Weekly loan summary')

@section('content')
<div class="container">
    <h1 class="mb-4">Weekly Loan Activity Summary</h1>

    <div class="card">
        <div class="card-body">
            <p>Dear Admin,</p>
            <p>Please find the below summary of your loan.</p>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Number of Loan Applications This Week:</strong> {{ $loan_summary['loanApplicationsCount'] }}
                </li>
                <li class="list-group-item">
                    <strong>Number of Loans Disbursed This Week:</strong> {{ $loan_summary['loansDisbursedCount'] }}
                </li>
                <li class="list-group-item">
                    <strong>Total Value of Loans Disbursed This Week:</strong> {{ $loan_summary['totalLoansDisbursed'] }}
                </li>
                <li class="list-group-item">
                    <strong>Loans That Fell Due This Week:</strong> {{ $loan_summary['loansDueThisWeekCount'] }}
                </li>
                <li class="list-group-item">
                    <strong>Total Amount Due This Week:</strong> {{ $loan_summary['totalDueThisWeek'] }}
                </li>
                <li class="list-group-item">
                    <strong>Total Amount Repaid This Week:</strong> {{ $loan_summary['totalRepaidThisWeek'] }}
                </li>
            </ul>
            <p>Thank you.</p>
            <h5>GnuGrid LMS Team</h5>
        </div>
    </div>
</div>
@endsection