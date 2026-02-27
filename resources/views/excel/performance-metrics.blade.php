@extends('excel.layouts')

@section('content')
    <table class="table">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center; font-weight: bold; font-size: 16px">
                    {{ $partner->Institution_Name }}</th>
            </tr>
            <tr>
                <th colspan="2" style="text-align: center; font-weight: bold; font-size: 14px;">Performance Metrics Report
                </th>
            </tr>
            <tr>
                <th colspan="2" style="text-align: center; font-weight: bold; font-size: 12px;">From:
                    {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
            </tr>
            <tr>
                <th style="font-weight: bold;">Description</th>
                <th style="text-align: right; font-weight: bold">Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 400px">Total Number of Borrowers </td>
                <td style="text-align: right;">{{ number_format($records->asAt->borrowers_count) }}</td>
            </tr>
            <tr>
                <td>Number of Active Borrowers at {{ $filters['endDate'] }}</td>
                <td style="text-align: right;">{{ number_format($records->asAt->active_borrowers_count) }}</td>
            </tr>
            <tr>
                <td>Principal Portfolio Disbursed</td>
                <td style="text-align: right;">{{ number_format($records->asAt->principal_portfolio) }}</td>
            </tr>
            <tr>
                <td>No. of Fully Paid Loans</td>
                <td style="text-align: right;">{{ number_format($records->inRange->paid_loans_count) }}</td>
            </tr>
            <tr>
                <td>No. of Active Loans </td>
                <td style="text-align: right;">{{ number_format($records->asAt->open_loans_count) }}</td>
            </tr>
            <tr>
                <td>Opening Balance </td>
                <td style="text-align: right;">{{ number_format($records->asAt->opening_balance) }}</td>
            </tr>
            <tr>
                <td>Closing Balance </td>
                <td style="text-align: right;">{{ number_format($records->asAt->closing_balance) }}</td>
            </tr>
            <tr>
                <td>Total Outstanding Balance (Active Loans) </td>
                <td style="text-align: right;">{{ number_format($records->asAt->active_outstanding_balance) }}</td>
            </tr>
            <tr>
                <td>Payments - Principal</td>
                <td style="text-align: right;">{{ number_format($records->inRange->principal_paid) }}</td>
            </tr>
            <tr>
                <td>Payments - Interest</td>
                <td style="text-align: right;">{{ number_format($records->inRange->interest_paid) }}</td>
            </tr>
            <tr>
                <td>Payments - Fees
                </td>
                <td style="text-align: right;">{{ number_format($records->inRange->fees_paid) }}</td>
            </tr>
            <tr>
                <td>Payments - Penalty
                </td>
                <td style="text-align: right;">{{ number_format($records->inRange->penalties_paid) }}</td>
            </tr>
            <tr>
                <td>Recovery Rate (All Loans) </td>
                <td style="text-align: right;">
                    @if (($allPrincipal = $records->asAt->principal_paid + $records->asAt->principal_past_due) === 0)
                        0%
                    @else
                        {{ round(($records->asAt->principal_paid / $allPrincipal) * 100) }}%
                    @endif
                </td>
            </tr>
            <tr>
                <td>Recovery Rate (Active Loans) </td>
                <td style="text-align: right;">
                    @if (($allPrincipal = $records->asAt->active_principal_paid + $records->asAt->active_principal_past_due) === 0)
                        0%
                    @else
                        {{ round(($records->asAt->active_principal_paid / $allPrincipal) * 100) }}%
                    @endif
                </td>
            </tr>
            <tr>
                <td>Portfolio at Risk - PAR 30 </td>
                <td style="text-align: right;">{{ number_format($records->asAt->par_30) }}</td>
            </tr>
            <tr>
                <td>Portfolio at Risk - PAR 60 </td>
                <td style="text-align: right;">{{ number_format($records->asAt->par_60) }}</td>
            </tr>
            <tr>
                <td>Portfolio at Risk - PAR 90 </td>
                <td style="text-align: right;">{{ number_format($records->asAt->par_90) }}</td>
            </tr>
        </tbody>
    </table>
    <x-print-footer />
@endsection
