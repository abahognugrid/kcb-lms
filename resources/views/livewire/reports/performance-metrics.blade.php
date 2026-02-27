<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-3">
                <h5 class="mb-0">Performance Metrics Report</h5>
            </div>
            <div class="col-md-9 d-flex justify-content-end align-items-center">
                <x-date-filter />
                <x-export-buttons :with-excel="false" />
            </div>
        </div>
    </div>

    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-end">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Number of Borrowers </td>
                    <td class="text-end">{{ number_format($records->asAt->borrowers_count) }}</td>
                </tr>
                <tr>
                    <td>Number of Active Borrowers </td>
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
                    <td class="text-end">{{ number_format($records->asAt->active_loans_count) }}</td>
                </tr>
                <tr>
                    <td>Opening Balance</td>
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
                    <td>Gnugrid Fees
                    </td>
                    <td class="text-end">{{ number_format($records->inRange->gnugrid_fees_paid) }}</td>
                </tr>
                <tr>
                    <td>Payments - Penalty
                    </td>
                    <td class="text-end">{{ number_format($records->inRange->penalties_paid) }}</td>
                </tr>
                <tr>
                    <td>Recovery Rate (All Loans) </td>
                    <td class="text-end">
                        @php
                            $principalPaid = $records->asAt->principal_paid ?? 0;
                            $principalPastDue = $records->asAt->principal_past_due ?? 0;
                            $allPrincipal = $principalPaid + $principalPastDue;
                        @endphp

                        @if ($allPrincipal > 0)
                            {{ round(($principalPaid / $allPrincipal) * 100) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Recovery Rate (Active Loans) </td>
                    <td class="text-end">
                        @php
                            $principalPaid = $records->asAt->active_principal_paid ?? 0;
                            $principalPastDue = $records->asAt->active_principal_past_due ?? 0;
                            $allPrincipal = $principalPaid + $principalPastDue;
                        @endphp

                        @if ($allPrincipal > 0)
                            {{ round(($principalPaid / $allPrincipal) * 100) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @foreach($records->par as $ageClass => $ageOutstanding)
                    <tr>
                        <td>Portfolio at Risk - {{ str($ageClass)->upper()->replace('_', ' ') }} </td>
                        <td class="text-end">{{ number_format($ageOutstanding) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
