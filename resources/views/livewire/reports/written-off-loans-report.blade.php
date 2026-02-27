<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                @if ($showRecoveries)
                    <h5 class="mb-0">Written Off Loans Recovered Report</h5>
                @else
                    <h5 class="mb-0">Written Off Loans Report</h5>
                @endif
            </div>
            <div class="col-md-8 d-flex justify-content-end">
                <select class="form-select w-25 mx-3" wire:model.live="loanProductId" name="loanProductId">
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
        <table id="report-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Loan #</th>
                    <th>Customer Name</th>
                    <th class="text-end">Phone Number</th>
                    <th class="text-end">Amount Disbursed</th>
                    <th class="text-end">Amount Written Off</th>
                    <th class="text-end">Date Written Off</th>
                    @if ($showRecoveries)
                        <th class="text-end">Amount Recovered</th>
                        <th class="text-end">Balance After Recovery</th>
                        <th class="text-end">Date Last Recovered</th>
                    @endif
                    <th>Written Off By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->account_number }}</td>
                        <td>{{ $record->customer->name }}</td>
                        <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                        <td class="text-end"><x-money :value="$record->Facility_Amount_Granted" /></td>
                        <td class="text-end"><x-money :value="$record->Written_Off_Amount" /></td>
                        <td class="text-end">{{ $record->Written_Off_Date->format('d-m-Y') }}</td>
                        @if ($showRecoveries)
                            <td class="text-end"><x-money :value="$record->Written_Off_Amount_Recovered" /></td>
                            <td class="text-end"><x-money :value="$record->Written_Off_Amount - $record->Written_Off_Amount_Recovered" /></td>
                            <td class="text-end">{{ $record->Last_Recovered_At }}</td>
                        @endif
                        <td>
                            @if ($wriitenOfficerName = $record->writtenOffBy?->name)
                                {{ $wriitenOfficerName }}
                            @else
                                <i>System Auto Write-off</i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Totals</th>
                    <th class="text-end">{{ $records->count() }}</th>
                    <th></th>
                    <th class="text-end"><x-money :value="$records->sum('Facility_Amount_Granted')" /></th>
                    <th class="text-end"><x-money :value="$records->sum('Written_Off_Amount')" /> </th>
                    <th></th>
                    @if ($showRecoveries)
                        <th class="text-end"><x-money :value="$records->sum('Written_Off_Amount_Recovered')" /></th>
                        <th class="text-end"><x-money :value="$records->sum('Written_Off_Amount') - $records->sum('Written_Off_Amount_Recovered')" /></th>
                        <th></th>
                    @endif
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer pagination mt-5 d-flex justify-content-end">
        {{ $records->links() }}
    </div>
</div>
