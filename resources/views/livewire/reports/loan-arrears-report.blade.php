<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-2">
                <h5 class="mb-0">Arrears Report</h5>
            </div>
            <div class="col-md-10 d-flex justify-content-end">
                <select class="form-select w-20" wire:model.live="loanProductId" name="loanProductId">
                    <option value="">Select Loan Product</option>
                    @foreach ($loanProducts as $loanProductId => $loanProductName)
                        <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                    @endforeach
                </select>
                <div class="d-flex align-items-center mx-2">
                    <input type="checkbox" id="exclude-written-off-loans" wire:model.change="excludeWrittenOffLoans"
                           class="form-check-input px-2 py-1 mx-2 me-2">
                    <label for="exclude-written-off-loans" class="me-2 form-check-label fs-6">Exclude Written-off</label>
                </div>
                <x-checkbox />
                <x-end-date />
                <x-export-buttons />
            </div>
        </div>
    </div>
    <div class="card-body">
        <x-session-flash />
        <div class="table-responsive overflow-x-auto">
            <table id="report-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan="4"></th>
                        <th colspan="4" class="text-center">Outstanding</th>
                        <th colspan="{{ $suspendedInterest ? 1 : 4 }}" class="text-center">@if($suspendedInterest) Suspended @else Arrears @endif </th>
                        <th colspan="4"></th>
                    </tr>
                    <tr>
                        <th>Loan #</th>
                        <th>Customer</th>
                        <th>Phone Number</th>
                        <th class="text-end">Amount Disbursed</th>
                        <th class="text-end">Principal</th>
                        <th class="text-end">Interest</th>
                        <th class="text-end">Penalty</th>
                        <th class="text-end">Total</th>
                        @if(!$suspendedInterest)
                        <th class="text-end">Principal</th>
                        @endif
                        <th class="text-end">Interest</th>
                        @if(!$suspendedInterest)
                        <th class="text-end">Penalty</th>
                        <th class="text-end">Total</th>
                        @endif
                        <th class="text-end">Arrears Rate</th>
                        <th class="text-end">Days in Arrears</th>
                        <th class="text-end" style="min-width: 100px">Expiry Date</th>
                        <th class="text-end" style="min-width: 100px">Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->customer->name }}</td>
                            <td>{{ $record->customer->Telephone_Number }}</td>
                            <td class="text-end"><x-money :value="$record->Facility_Amount_Granted" /></td>
                            <td class="text-end"><x-money :value="$record->principal_outstanding" /></td>
                            <td class="text-end"><x-money :value="$record->interest_outstanding" /></td>
                            <td class="text-end"><x-money :value="$record->penalty_outstanding" /></td>
                            <td class="text-end"><x-money :value="$record->total_outstanding_amount" /></td>
                            @if(!$suspendedInterest)
                            <td class="text-end"><x-money :value="$record->total_principal_arrears" /></td>
                            @endif
                            <td class="text-end"><x-money :value="$record->total_interest_arrears" /></td>
                            @if(!$suspendedInterest)
                            <td class="text-end"><x-money :value="$record->penalty_arrears" /></td>
                            <td class="text-end"><x-money :value="$record->total_arrears_amount" /></td>
                            @endif
                            <td class="text-end">{{ $record->arrears_rate }}%</td>
                            <td class="text-end">{{ $record->arrear_days < 0 ? abs($record->arrear_days) : 0 }}</td>
                            <td class="text-end">{{ $record->Maturity_Date->format('Y-m-d') }}</td>
                            <td class="text-end">{{ $record->due_date ? $record->due_date: 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $suspendedInterest ? 12 : 15 }}" class="text-center">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th>Totals</th>
                        <th class="text-end">{{ count($records) }}</th>
                        <th></th>
                        <th class="text-end"><x-money :value="$records->sum('Facility_Amount_Granted')" /></th>
                        <th class="text-end">{{ number_format($records->sum('principal_outstanding')) }}</th>
                        <th class="text-end">{{ number_format($records->sum('interest_outstanding')) }}</th>
                        <th class="text-end">{{ number_format($records->sum('penalty_outstanding')) }}</th>
                        <th class="text-end">{{ number_format($records->sum('total_outstanding_amount')) }}</th>
                        @if(!$suspendedInterest)
                        <th class="text-end">{{ number_format($records->sum('total_principal_arrears')) }}</th>
                        @endif
                        <th class="text-end">{{ number_format($records->sum('total_interest_arrears')) }}</th>
                        @if(!$suspendedInterest)
                        <th class="text-end">{{ number_format($records->sum('penalty_arrears')) }}</th>
                        <th class="text-end">{{ number_format($records->sum('total_arrears_amount')) }}</th>
                        @endif
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer pagination mt-5 d-flex justify-content-end">
        {{ $records->links() }}
    </div>
</div>
