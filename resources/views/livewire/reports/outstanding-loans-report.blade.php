<div class="card">
    <div class="card-header ">
        <x-session-flash/>
        <div class="row">
            <div class="col-md-3">
                <h5 class="mb-0">Outstanding Loans Report</h5>
            </div>
            <div class="col-md-9 d-flex justify-content-end">
                <select class="form-select w-50 d-flex align-items-center mx-2" wire:model.live="loanProductId" name="loanProductId">
                    <option value="">Select Loan Product</option>
                    @foreach ($loanProducts as $loanProductId => $loanProductName)
                        <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                    @endforeach
                </select>
                <div class="d-flex align-items-center mx-2 w-50">
                    <input type="checkbox" id="include-written-off-loans" class="form-check-input mx-2" wire:model.change="includeWrittenOffLoans">
                    <label for="include-written-off-loans" class="me-2 form-check-label fs-6">Include Written Off</label>
                </div>
                <x-end-date/>
                <x-export-buttons/>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="report-table" class="table table-bordered table-sm wrap">
                <thead>
                <tr>
                    <th colspan="7"></th>
                    <th colspan="4" class="text-center">Outstanding</th>
                    <th colspan="2"></th>
                </tr>
                <tr>
                    <th style="min-width: 80px">Loan #</th>
                    <th style="min-width: 200px">Customer</th>
                    <th>Phone Number</th>
                    <th class="text-end">Amount Disbursed</th>
                    <th class="text-end" style="min-width: 100px">Date Disbursed</th>
                    <th class="text-end" style="min-width: 100px">Due Date</th>
                    <th class="text-end" style="min-width: 100px">Expiry Date</th>
                    <th class="text-end" style="min-width: 100px">Days to Expiry</th>
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
                        <td>{{ $record->customer->Telephone_Number }}</td>
                        <td class="text-end">{{ number_format($record->Facility_Amount_Granted) }}</td>
                        <td class="text-end">{{ $record->Credit_Account_Date->format('Y-m-d') }}</td>
                        <td class="text-end">{{ $record->due_date ? $record->due_date: 'N/A' }}</td>
                        <td class="text-end">{{ $record->Maturity_Date->format('Y-m-d') }}</td>
                        <td class="text-end">{{ $record->days_to_expiry }}</td>
                        <td class="text-end">{{ number_format($record->principal_outstanding) }}</td>
                        <td class="text-end">{{ number_format($record->interest_outstanding) }}</td>
                        <td class="text-end">{{ number_format($record->penalty_outstanding) }}</td>
                        <td class="text-end">
                            {{ number_format($record->principal_outstanding + $record->interest_outstanding + $record->penalty_outstanding) }}
                        </td>
                        <td class="text-end">{{ number_format($record->total_past_due) }}</td>
                        <td class="text-end">{{ number_format($record->total_pending_due) }}</td>
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
                    <th class="text-end"></th>
                    <th class="text-end"><x-money :value="$records->sum('principal_outstanding')"/></th>
                    <th class="text-end">
                        <x-money :value="$records->sum('interest_outstanding')" />
                    </th>
                    <th class="text-end">
                        <x-money :value="$records->sum('penalty_outstanding')" />
                    </th>
                    <th class="text-end">
                        <x-money :value="$records->sum('principal_outstanding') + $records->sum('interest_outstanding') + $records->sum('penalty_outstanding')" />
                    </th>
                    <th class="text-end"><x-money :value="$records->sum('total_past_due')" /></th>
                    <th class="text-end"><x-money :value="$records->sum('total_pending_due')" /></th>
                </tr>
                </tfoot>
            </table>

            <div class="pagination mt-5 d-flex justify-content-end">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
