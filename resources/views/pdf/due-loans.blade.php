@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ data_get($filters, 'partnerName') }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Due Loans Report</h4>
        <p style="font-size: 10px; margin-bottom: 4px;">As at: {{ data_get($filters, 'endDate') }}</p>
    </div>
    <table id="report-table" class="table table-bordered" style="border-top: 1px solid #c8c3c3;">
        <thead>
            <tr>
                <th class="text-end">Loan#</th>
                <th class="text-start">Name</th>
                <th class="text-start">Phone Number</th>
                <th class="text-end">Date Disbursed</th>
                <th class="text-end">Amount Disbursed</th>
                <th class="text-end">Principal Balance</th>
                <th class="text-end">Amount Due</th>
                <th class="text-end">Amount Paid</th>
                <th class="text-end">Past Due</th>
                <th class="text-end">Pending Due</th>
                <th class="text-end">Expiry Date</th>
                <th class="text-end">Last Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td class="text-end">{{ $record->id }}</td>
                    <td>{{ $record->customer->name }}</td>
                    <td>{{ $record->customer->Telephone_Number }}</td>
                    <td class="text-end">{{ $record->Credit_Account_Date->toDateString() }}</td>
                    <td class="text-end">{{ number_format($record->Facility_Amount_Granted) }}</td>
                    <td class="text-end">{{ number_format($record->schedule_sum_principal_remaining) }}</td>
                    <td class="text-end">{{ number_format($record->schedule_sum_total_outstanding) }}</td>
                    <td class="text-end">
                        {{ number_format($record->schedule_sum_total_payment - $record->schedule_sum_total_outstanding) }}
                    </td>
                    <td class="text-end">{{ number_format($record->past_due) }}</td>
                    <td class="text-end">{{ number_format($record->pending_due) }}</td>
                    <td class="text-end">{{ $record->schedule_max_payment_due_date }}</td>
                    <td class="text-end">{{ $record->last_payment_date?->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">No records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>Totals</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th colspan="2"></th>
                <th class="text-end">{{ number_format($records->sum('Facility_Amount_Granted')) }}</th>
                <th class="text-end">{{ number_format($records->sum('schedule_sum_principal_remaining')) }}</th>
                <th class="text-end">{{ number_format($records->sum('schedule_sum_total_outstanding')) }}</th>
                <th class="text-end">
                    {{ number_format($records->sum('schedule_sum_total_payment') - $records->sum('schedule_sum_total_outstanding')) }}
                </th>
                <th class="text-end">{{ number_format($records->sum('past_due')) }}</th>
                <th class="text-end">{{ number_format($records->sum('pending_due')) }}</th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
