<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 18px; font-weight: bold;">
                {{ $partner->Institution_Name }}</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 14px; font-weight: bold;">Paid Off Loans Report</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 10px;">As at:
                {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
        <tr class="table-header">
            <th style="font-weight: bold; width: 80px;">Loan #</th>
            <th style="font-weight: bold; width: 180px;">Name</th>
            <th style="text-align: right; font-weight: bold; width: 140px">Phone Number</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Loan Amount</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Date Disbursed</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Expiry Date</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Amount Paid</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($loans as $loan)
            <tr>
                <td>{{ $loan->id }}</td>
                <td>{{ $loan->customer->name }}</td>
                <td style="text-align: right; ">{{ $loan->customer->Telephone_Number }}</td>
                <td style="text-align: right; ">{{ number_format($loan->Facility_Amount_Granted) }}</td>
                <td style="text-align: right; ">{{ $loan->Credit_Account_Date->format('d-m-Y') }}</td>
                <td style="text-align: right; ">{{ $loan->Maturity_Date->format('d-m-Y') }}</td>
                <td style="text-align: right; ">{{ number_format($loan->loan_repayments_sum_amount) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Totals</th>
            <th style="text-align: right; font-weight: bold;">{{ $loans->count() }}</th>
            <th></th>
            <th style="text-align: right; font-weight: bold;">
                {{ number_format($loans->sum('Facility_Amount_Granted')) }}</th>
            <th></th>
            <th></th>
            <th style="text-align: right; font-weight: bold;">
                {{ number_format($loans->sum('loan_repayments_sum_amount')) }}</th>
        </tr>
    </tfoot>
</table>
<x-print-footer />
