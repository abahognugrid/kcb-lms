@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partner->Institution_Name }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Performance Metrics Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th class="text-start">Description</th>
                <th class="text-end">Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Number of Registered Borrowers </td>
                <td class="text-end">{{ number_format($records->asAt->borrowers_count) }}</td>
            </tr>
            <tr>
                <td>Number of Active Borrowers at {{ $filters['endDate'] }}</td>
                <td class="text-end">{{ number_format($records->asAt->active_borrowers_count) }}</td>
            </tr>
            <tr>
                <td>Principal Portfolio Disbursed</td>
                <td class="text-end">{{ number_format($records->asAt->principal_portfolio) }}</td>
            </tr>
            <tr>
                <td>No. of Fully Paid Loans </td>
                <td class="text-end">{{ number_format($records->inRange->paid_loans_count) }}</td>
            </tr>
            <tr>
                <td>No. of Active Loans </td>
                <td class="text-end">{{ number_format($records->asAt->open_loans_count) }}</td>
            </tr>
            <tr>
                <td>Opening Balance </td>
                <td class="text-end">{{ number_format($records->asAt->opening_balance) }}</td>
            </tr>
            <tr>
                <td>Closing Balance </td>
                <td class="text-end">{{ number_format($records->asAt->closing_balance) }}</td>
            </tr>
            <tr>
                <td>Total Outstanding Balance (Active Loans) <span class="text-info">***</span></td>
                <td class="text-end">{{ number_format($records->asAt->active_outstanding_balance) }}</td>
            </tr>
            <tr>
                <td>Payments - Principal </td>
                <td class="text-end">{{ number_format($records->inRange->principal_paid) }}</td>
            </tr>
            <tr>
                <td>Payments - Interest </td>
                <td class="text-end">{{ number_format($records->inRange->interest_paid) }}</td>
            </tr>
            <tr>
                <td>Payments - Fees
                </td>
                <td class="text-end">{{ number_format($records->inRange->fees_paid) }}</td>
            </tr>
            <tr>
                <td>Payments - Penalty
                </td>
                <td class="text-end">{{ number_format($records->inRange->penalties_paid) }}</td>
            </tr>
            <tr>
                <td>Recovery Rate (All Loans) </td>
                <td class="text-end">
                    @if (($allPrincipal = $records->asAt->principal_paid + $records->asAt->principal_past_due) === 0)
                        0%
                    @else
                        {{ round(($records->asAt->principal_paid / $allPrincipal) * 100) }}%
                    @endif
                </td>
            </tr>
            <tr>
                <td>Recovery Rate (Active Loans) </td>
                <td class="text-end">
                    @if (($allPrincipal = $records->asAt->active_principal_paid + $records->asAt->active_principal_past_due) === 0)
                        0%
                    @else
                        {{ round(($records->asAt->active_principal_paid / $allPrincipal) * 100) }}%
                    @endif
                </td>
            </tr>
            <tr>
                <td>Portfolio at Risk - PAR 30 </td>
                <td class="text-end">{{ number_format($records->asAt->par_30) }}</td>
            </tr>
            <tr>
                <td>Portfolio at Risk - PAR 60 </td>
                <td class="text-end">{{ number_format($records->asAt->par_60) }}</td>
            </tr>
            <tr>
                <td>Portfolio at Risk - PAR 90 </td>
                <td class="text-end">{{ number_format($records->asAt->par_90) }}</td>
            </tr>
        </tbody>
    </table>
    <x-print-footer />
@endsection
