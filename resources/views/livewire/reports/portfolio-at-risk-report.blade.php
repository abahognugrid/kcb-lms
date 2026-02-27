<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">Portfolio at Risk Report</h5>
            </div>
            <div class="col-md-8 d-flex justify-content-end align-items-center">
                <select class="form-select w-25 mx-3" wire:model.live="loanProductId" name="loanProductId">
                    <option value="">Select Loan Product</option>
                    @foreach ($loanProducts as $loanProductId => $loanProductName)
                        <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                    @endforeach
                </select>
                <div class="me-4">
                    <label class="d-flex align-items-center m-0">
                        <input type="checkbox" class="form-check mb-0 me-2" wire:model.live="excludeNotDue">
                        <span>Exclude Not Due</span>
                    </label>
                </div>
                <x-end-date/>
                <x-export-buttons/>
            </div>
        </div>
    </div>
    <div class="card-body">
        <x-session-flash/>
        <div class="table-responsive">
            <table id="report-table" class="table table-bordered">
                <thead>
                <tr>
                    <th colspan="6"></th>
                    <th colspan="11" class="text-center">Principal in Arrears</th>
                </tr>
                <tr>
                    <th class="text-nowrap">Loan #</th>
                    <th class="text-nowrap">Name</th>
                    <th class="text-nowrap text-end">Phone Number</th>
                    <th class="text-end">Amount Disbursed</th>
                    <th class="text-end">Maturity Date</th>
                    <th class="text-end">Principal Outstanding</th>
                    <th class="text-end">Arrears Principal</th>
                    @foreach($ageingDays as $provision)
                        <th class="text-nowrap text-end">{{ $provision->days }} <br/>days</th>
                        <th class="text-end">PAR</th>
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
                        <td class="text-nowrap text-end">{{ $loan->Maturity_Date->format('d-m-Y') }}</td>
                        <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding"/></td>
                        <td class="text-nowrap text-end"><x-money :value="$loan->principal_in_arrears"/></td>
                        <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding_at_30"/></td>
                        <td class="text-nowrap text-end">{{ percentage($loan->principal_outstanding_at_30, $loan->schedule_sum_principal_remaining) }}%</td>
                        <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding_at_60"/></td>
                        <td class="text-nowrap text-end">{{ percentage($loan->principal_outstanding_at_60, $loan->schedule_sum_principal_remaining) }}%</td>
                        <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding_at_90"/></td>
                        <td class="text-nowrap text-end">{{ percentage($loan->principal_outstanding_at_90, $loan->schedule_sum_principal_remaining) }}%</td>
                        <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding_at_180"/></td>
                        <td class="text-nowrap text-end">{{ percentage($loan->principal_outstanding_at_180, $loan->schedule_sum_principal_remaining) }}%</td>
                        <td class="text-nowrap text-end"><x-money :value="$loan->principal_outstanding_after_180"/></td>
                        <td class="text-nowrap text-end">{{ percentage($loan->principal_outstanding_after_180, $loan->schedule_sum_principal_remaining) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="17">No records found</td>
                    </tr>
                @endforelse
                </tbody>
                <tfoot>
                @php
                    $principal = $loans->sum('principal_outstanding')
                @endphp
                <tr>
                    <th class="text-nowrap">Totals</th>
                    <th class="text-end">{{ $loans->count() }}</th>
                    <th class="text-end"></th>
                    <th class="text-end"><x-money :value="$loans->sum('Facility_Amount_Granted')"/></th>
                    <th class="text-end"></th>
                    <th class="text-end"><x-money :value="$principal"/></th>
                    <th class="text-end"><x-money :value="$loans->sum('principal_in_arrears')"/></th>
                    <th class="text-end"><x-money :value="$totalAt30 = $loans->sum('principal_outstanding_at_30')"/></th>
                    <th class="text-end">{{ percentage($totalAt30, $principal) }}%</th>
                    <th class="text-end"><x-money :value="$totalAt60 = $loans->sum('principal_outstanding_at_60')"/></th>
                    <th class="text-end">{{ percentage($totalAt60, $principal) }}%</th>
                    <th class="text-end"><x-money :value="$totalAt90 = $loans->sum('principal_outstanding_at_90')"/></th>
                    <th class="text-end">{{ percentage($totalAt90, $principal) }}%</th>
                    <th class="text-end"><x-money :value="$totalAt180 = $loans->sum('principal_outstanding_at_180')"/></th>
                    <th class="text-end">{{ percentage($totalAt180, $principal) }}%</th>
                    <th class="text-end"><x-money :value="$totalAfter180 = $loans->sum('principal_outstanding_after_180')"/></th>
                    <th class="text-end">{{ percentage($totalAfter180, $principal) }}%</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer pagination mt-5 d-flex justify-content-end">
        {{ $loans->links() }}
    </div>
</div>
