@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Repayment Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>
    <table id="report-table" class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th colspan="5"></th>
                <th class="text-center" colspan="4">Payments Due</th>
                <th class="text-center" colspan="4">Payments Made</th>
                <th></th>
            </tr>
            <tr>
                <th>Loan #</th>
                <th class="">Customer</th>
                <th class="text-end">Phone Number</th>
                <th class="text-end">Loan Amount</th>
                <th class="text-end">Last Payment Date</th>
                <th class="text-end">Principal</th>
                <th class="text-end">Interest</th>
                <th class="text-end">Penalty</th>
                <th class="text-end">Fees</th>
                <th class="text-end">Principal</th>
                <th class="text-end">Interest</th>
                <th class="text-end">Penalty</th>
                <th class="text-end">Fees</th>
                <th class="text-end">Total Paid</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->customer->name }}</td>
                    <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                    <td class="text-end"><x-money :value="$record->Credit_Amount" /></td>
                    <td class="text-end">{{ $record->last_payment_date }}</td>
                    <td class="text-end"><x-money :value="$record->principal_due" /></td>
                    <td class="text-end"><x-money :value="$record->interest_due" /></td>
                    <td class="text-end"><x-money :value="$record->penalty_due" /></td>
                    <td class="text-end"><x-money :value="$record->fees_due" /></td>
                    <td class="text-end"><x-money :value="$record->principal_paid" /></td>
                    <td class="text-end"><x-money :value="$record->interest_paid" /></td>
                    <td class="text-end"><x-money :value="$record->penalty_paid" /></td>
                    <td class="text-end"><x-money :value="$record->fees_paid" /></td>
                    <td class="text-end"><x-money :value="$record->total_paid" /></td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center">No records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="">Totals</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th></th>
                <th class="text-end"><x-money :value="$records->sum('Credit_Amount')" /></th>
                <th></th>
                <th class="text-end"><x-money :value="$records->sum('principal_due')" /></th>
                <th class="text-end"><x-money :value="$records->sum('interest_due')" /></th>
                <th class="text-end"><x-money :value="$records->sum('penalty_due')" /></th>
                <th class="text-end"><x-money :value="$records->sum('fees_due')" /></th>
                <th class="text-end"><x-money :value="$records->sum('principal_paid')" /></th>
                <th class="text-end"><x-money :value="$records->sum('interest_paid')" /></th>
                <th class="text-end"><x-money :value="$records->sum('penalty_paid')" /></th>
                <th class="text-end"><x-money :value="$records->sum('fees_paid')" /></th>
                <th class="text-end"><x-money :value="$records->sum('total_paid')" /></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
