@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partner->Institution_Name }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Paid Off Loans Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table id="report-table" class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Loan #</th>
                <th class="text-start">Name</th>
                <th class="text-end">Phone Number</th>
                <th class="text-end">Loan Amount</th>
                <th class="text-end">Date Disbursed</th>
                <th class="text-end">Expiry Date</th>
                <th class="text-end">Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
                <tr>
                    <td>{{ $loan->id }}</td>
                    <td>{{ $loan->customer->name }}</td>
                    <td class="text-end">{{ $loan->customer->Telephone_Number }}</td>
                    <td class="text-end">
                        <x-money :value="$loan->Facility_Amount_Granted" />
                    </td>
                    <td class="text-end">{{ $loan->Credit_Account_Date->format('d-m-Y') }}</td>
                    <td class="text-end">{{ $loan->Maturity_Date->format('d-m-Y') }}</td>
                    <td class="text-end"><x-money :value="$loan->loan_repayments_sum_amount" /></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start">Totals</th>
                <th class="text-end">{{ $loans->count() }}</th>
                <th></th>
                <th class="text-end"><x-money :value="$loans->sum('Facility_Amount_Granted')" /></th>
                <th></th>
                <th></th>
                <th class="text-end"><x-money :value="$loans->sum('loan_repayments_sum_amount')" /> </th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
