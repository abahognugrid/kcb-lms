@extends('layouts/blankLayout')

@section('title', 'Daily loan summary')

@section('content')
<div class="container">
    <h1 class="mb-4">Todays Loan Activity Summary</h1>

    <div class="card">
        <div class="card-body">
            <p>Dear Admin,</p>
            <p>Please find the below summary of your loan.</p>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Number of Loan Applications:</strong> {{ $loan_summary['loanApplicationsCount'] }}
                </li>
                <li class="list-group-item">
                    <strong>Number of Loans Disbursed:</strong> {{ $loan_summary['loansDisbursedCount'] }}
                </li>
                <li class="list-group-item">
                    <strong>Total Value of Loans Disbursed:</strong> {{ $loan_summary['totalLoansDisbursed'] }}
                </li>
                <li class="list-group-item">
                    <strong>Loans Falling Due Today:</strong> {{ $loan_summary['loansDueTodayCount'] }}
                </li>
                <li class="list-group-item">
                    <strong>Total Amount Due Today:</strong> {{ $loan_summary['totalDueToday'] }}
                </li>
                <li class="list-group-item">
                    <strong>Total Amount Repaid Today:</strong> {{ $loan_summary['totalRepaidToday'] }}
                </li>
            </ul>
            <p>Thank you.</p>
            <h5>GnuGrid LMS Team</h5>
        </div>
    </div>
</div>
@endsection