<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">Pending Disbursement Report</h5>
            </div>
            <div class="col-md-8 d-flex justify-content-end">
                <select class="form-select w-50" wire:model.live="loanProductId" name="loanProductId">
                    <option value="">Select Loan Product</option>
                    @foreach ($loanProducts as $loanProductId => $loanProductName)
                        <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                    @endforeach
                </select>
                <x-date-filter/>
                <x-export-buttons/>
            </div>
        </div>
    </div>
    <div class="card-body">
        <x-session-flash/>
        <table id="report-table" class="table table-bordered">
            <thead>
            <tr>
                <th>Loan App. #</th>
                <th>Customer Name</th>
                <th class="text-end">Phone Number</th>
                <th class="text-end">Pending Amount</th>
                <th class="text-end">Approval Date</th>
                <th>Approved By</th>
            </tr>
            </thead>
            <tbody class="">
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->customer->name }}</td>
                    <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                    <td class="text-end">{{ $record->Amount }}</td>
                    <td>{{ $record->Last_Status_Change_Date }}</td> <!-- todo: Use approved at on loan or statuses -->
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">There are no pending disbursements.</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot class="fw-bold">
            <tr>
                <th>Totals</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th></th>
                <th class="text-end">{{ number_format($records->sum('Amount')) }}</th>
                <th colspan="2"></th>
            </tr>
            </tfoot>
        </table>
        <div class="pagination">
            {{ $records->links() }}
        </div>
    </div>
</div>
