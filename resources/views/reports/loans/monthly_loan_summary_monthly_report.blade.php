@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loan Reports - Monthly Loan Summary')
@section('content')

<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0">Monthly Loan Summary Report</h5>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <a class="export-csv btn btn-outline-dark" data-table-id="report-table" data-filename="report.csv" class="btn btn-outline-dark">Export CSV</a>
                    <a class="export-pdf btn btn-outline-dark" data-table-id="report-table" data-filename="report.pdf" class="btn btn-outline-dark">Export PDF</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="report-table" class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Metric</th>
                        @foreach ($months as $month)
                            <th class="text-end">{{ $month }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Principal Balance</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['principal_balance'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Principal Received</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['principal_received'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Interest Received</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['interest_received'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Fees Received</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['fees_received'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Penalty Received</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['penalty_received'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Total Received</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['total_received'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>New Loans</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['new_loans'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Number of Repayments</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['number_of_repayments'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Pending Due</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['pending_due'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Number of Fully Paid Loans</th>
                        @foreach ($months as $month)
                            <td class="text-end">{{ $reportData[$month]['number_of_fully_paid_loans'] }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
