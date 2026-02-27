<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">Ageing Report</h5>
            </div>
            <div class="col-md-8 d-flex justify-content-end">
                <select class="form-select w-50" wire:model.live="loanProductId" name="loanProductId">
                    <option value="">Select Loan Product</option>
                    @foreach ($loanProducts as $loanProductId => $loanProductName)
                        <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                    @endforeach
                </select>
                <div class="d-flex align-items-center mx-2 w-50">
                    <input type="checkbox" id="exclude-not-due" class="form-check mb-0 me-2" wire:model.live="excludeNotDue" name="excludeNotDue">
                    <label class="d-flex align-items-center m-0" for="exclude-not-due"><span>Exclude Not Due</span></label>
                </div>
                <x-end-date/>
                <x-export-buttons/>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="report-table" class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th colspan="9"></th>
                            <th colspan="5" class="text-center">Age Classes</th>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Loan #</th>
                            <th class="text-nowrap">Name</th>
                            <th class="text-nowrap text-end">Phone Number</th>
                            <th class="text-end">Amount Disbursed</th>
                            <th class="text-end">Date Disbursed</th>
                            <th class="text-end">Maturity Date</th>
                            <th class="text-end">Principal Outstanding</th>
                            <th class="text-end">Principal in Arrears</th>
                            <th class="text-end">Days in Arrears</th>
                            @foreach($ageingDays as $provision)
                                <th class="text-nowrap text-end">{{ str_replace(' ', '', $provision->days) }}<br>days</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td class="text-nowrap">{{ $loan->id }}</td>
                            <td class="text-nowrap">{{ $loan->customer->name }}</td>
                            <td class="text-nowrap text-end">{{ $loan->customer->Telephone_Number }}</td>
                            <td class="text-nowrap text-end"><x-money :value="$loan->Facility_Amount_Granted"/></td>
                            <td class="text-nowrap text-end">{{ $loan->Credit_Account_Date->format('d-m-Y') }}</td>
                            <td class="text-nowrap text-end">{{ $loan->Maturity_Date->format('d-m-Y') }}</td>
                            <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding"/></td>
                            <td class="text-nowrap text-end"><x-money :value="$loan->principal_in_arrears"/></td>
                            <td class="text-nowrap text-end">{{ $loan->days_in_arrears < 0 ? abs($loan->days_in_arrears) : 0 }}</td>
                            <td class="text-nowrap text-end">{{ number_format($loan->principal_outstanding_at_30) }}</td>
                            <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding_at_60"/></td>
                            <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding_at_90"/></td>
                            <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding_at_180"/></td>
                            <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding_after_180"/></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No records found</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-nowrap">Totals</th>
                            <th class="text-end">{{ $loans->count() }}</th>
                            <th class="text-end"></th>
                            <th class="text-end"><x-money :value="$loans->sum('Facility_Amount_Granted')"/></th>
                            <th class="text-end"></th>
                            <th class="text-end"></th>
                            <th class="text-end"><x-money :value="$loans->sum('principal_outstanding')"/></th>
                            <th class="text-end"><x-money :value="$loans->sum('principal_in_arrears')"/></th>
                            <th class="text-end"></th>
                            <th class="text-end"><x-money :value="$loans->sum('principal_outstanding_at_30')"/></th>
                            <th class="text-end"><x-money :value="$loans->sum('principal_outstanding_at_60')"/></th>
                            <th class="text-end"><x-money :value="$loans->sum('principal_outstanding_at_90')"/></th>
                            <th class="text-end"><x-money :value="$loans->sum('principal_outstanding_at_180')"/></th>
                            <th class="text-end"><x-money :value="$loans->sum('principal_outstanding_after_180')"/></th>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
    <div class="card-footer pagination mt-5 d-flex justify-content-end">
        {{ $loans->links() }}
    </div>
</div>
