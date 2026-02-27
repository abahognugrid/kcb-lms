<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">Loan Applications Report</h5>
            </div>
            <div class="col-md-8 d-flex justify-content-end gap-2">
                <select class="form-select w-50" wire:model.live="loanProductId" name="loanProductId">
                    <option value="">Select Loan Product</option>
                    @foreach ($loanProducts as $loanProductId => $loanProductName)
                        <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                    @endforeach
                </select>
                <x-date-filter />
                <x-export-buttons />
                <button class="btn btn-outline-dark btn-sm" wire:click="exportCap">
                    <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                    <span>Export CAP</span>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <table id="report-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Loan #</th>
                    <th class="text-start">Customer</th>
                    <th class="text-end">Phone Number</th>
                    <th class="text-start">Product Name</th>
                    <th class="text-start">Purpose</th>
                    <th class="text-end">Application Date</th>
                    <th class="text-end">Amount Applied</th>
                    <th class="text-end">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->customer->name }}</td>
                        <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                        <td>{{ $record->loan_product->Name }}</td>
                        <td>{{ $record->Loan_Purpose }}</td>
                        <td class="text-end">
                            {{ \Carbon\Carbon::parse($record->Credit_Application_Date)->format('d M, Y') }}</td>
                        <td class="text-end"><x-money :value="$record->Amount" /></td>
                        <td>{{ $record->Credit_Application_Status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No records found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th>Totals</th>
                    <th class="text-end">{{ $records->count() }}</th>
                    <th colspan="4"></th>
                    <th class="text-end"><x-money :value="$records->sum('Facility_Amount_Granted')" /></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <div class="pagination mt-5 d-flex justify-content-end">
            {{ $records->links() }}
        </div>
    </div>
</div>
