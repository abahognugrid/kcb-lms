@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Outstanding Loans Report</h4>
        <p style="margin-top: 0; font-size: 10px">As at: {{ $filters['endDate'] }}</p>
    </div>

    <table id="report-table" class="table table-bordered wrap">
        <thead>
        <tr class="table-header">
            <th class="text-start">Loan #</th>
            <th class="text-start">Customer</th>
            <th class="text-end">Phone Number</th>
            <th class="text-end">Amount Disbursed</th>
            <th class="text-end">Date Disbursed</th>
            <th class="text-end">Expiry Date</th>
            <th class="text-end">Days to Expiry</th>
            <th class="text-end">Principal</th>
            <th class="text-end">Interest</th>
            <th class="text-end">Penalty</th>
            <th class="text-end">Total Balance</th>
            <th class="text-end">Arrears Amount</th>
            <th class="text-end">Pending Due</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->customer->name }}</td>
                <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                <td class="text-end"><x-money :value="$record->Facility_Amount_Granted"/></td>
                <td class="text-end">{{ $record->Credit_Account_Date->format('d-m-Y') }}</td>
                <td class="text-end">{{ $record->Maturity_Date->format('d-m-Y') }}</td>
                <td class="text-end">{{ $record->days_to_expiry }}</td>
                <td class="text-end"><x-money :value="$record->schedule_sum_principal_remaining" /></td>
                <td class="text-end"><x-money :value="$record->schedule_sum_interest_remaining" /></td>
                <td class="text-end"><x-money :value="$record->penalty_amount" /></td>
                <td class="text-end"><x-money :value="$record->schedule_sum_total_outstanding + $record->penalty_amount" /></td>
                <td class="text-end"><x-money :value="$record->total_past_due" /></td>
                <td class="text-end"><x-money :value="$record->total_pending_due" /></td>
            </tr>
        @empty
            <tr>
                <td colspan="13" class="text-center">No records found</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>Totals</th>
                <th class="text-end">{{ count($records) }}</th>
                <th></th>
                <th class="text-end"><x-money :value="$records->sum('Facility_Amount_Granted')"/></th>
                <th class="text-end"></th>
                <th class="text-end"></th>
                <th class="text-end"></th>
                <th class="text-end"><x-money :value="$records->sum('schedule_sum_principal_remaining')" /></th>
                <th class="text-end"><x-money :value="$records->sum('schedule_sum_interest_remaining')" /></th>
                <th class="text-end"><x-money :value="$records->sum('penalty_amount')" /></th>
                <th class="text-end"><x-money :value="$records->sum('schedule_sum_total_outstanding') + $records->sum('penalty_amount')" /></th>
                <th class="text-end"><x-money :value="$records->sum('total_past_due')" /></th>
                <th class="text-end"><x-money :value="$records->sum('total_pending_due')" /></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer/>
@endsection
