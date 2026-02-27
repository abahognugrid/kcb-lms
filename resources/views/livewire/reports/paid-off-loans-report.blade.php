<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">Paid Off Loans Report</h5>
            </div>
            <div class="col-md-8 d-flex justify-content-end">
                <select class="form-select w-50 mx-2" wire:model.live="loanProductId" name="loanProductId">
                    <option value="">Select Loan Product</option>
                    @foreach ($loanProducts as $loanProductId => $loanProductName)
                        <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                    @endforeach
                </select>
                <x-date-filter />
                <x-export-buttons />
            </div>
        </div>
    </div>

    <div class="card-body">
        <x-session-flash />
        <table id="report-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Loan #</th>
                    <th>Name</th>
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
                    <th>Totals</th>
                    <th class="text-end">{{ $loans->count() }}</th>
                    <th></th>
                    <th class="text-end"><x-money :value="$loans->sum('Facility_Amount_Granted')" /></th>
                    <th></th>
                    <th></th>
                    <th class="text-end"><x-money :value="$loans->sum('loan_repayments_sum_amount')" /> </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
