<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">Due Loans Report</h5>
            </div>
            <div class="col-md-8 d-flex justify-content-end">
                <select class="form-select w-25 d-flex align-items-center mx-2" wire:model.live="loanProductId" name="loanProductId">
                    <option value="">Select Loan Product</option>
                    @foreach ($loanProducts as $loanProductId => $loanProductName)
                        <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                    @endforeach
                </select>
                <x-end-date />
                <x-export-buttons />
            </div>
        </div>
    </div>
    <div class="card-body">
        <x-session-flash />
        <div class="table-responsive overflow-x-auto">
            <table class="table table-bordered">
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
                        <td class="text-nowrap">{{ $record->customer->name }}</td>
                        <td class="text-nowrap">{{ $record->customer->Telephone_Number }}</td>
                        <td class="text-end text-nowrap">{{ $record->Credit_Account_Date->toDateString() }}</td>
                        <td class="text-end">{{ number_format($record->Facility_Amount_Granted) }}</td>
                        <td class="text-end">{{ number_format($record->schedule_sum_principal_remaining) }}</td>
                        <td class="text-end">{{ number_format($record->schedule_sum_total_outstanding) }}</td>
                        <td class="text-end">
                            {{ number_format($record->schedule_sum_total_payment - $record->schedule_sum_total_outstanding) }}
                        </td>
                        <td class="text-end">{{ number_format($record->past_due) }}</td>
                        <td class="text-end">{{ number_format($record->pending_due) }}</td>
                        <td class="text-end text-nowrap">{{ $record->schedule_max_payment_due_date }}</td>
                        <td class="text-end text-nowrap">{{ $record->last_payment_date?->toDateString() }}</td>
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
        </div>
    </div>
    <div class="card-footer">
        {{ $records->links() }}
    </div>
</div>
