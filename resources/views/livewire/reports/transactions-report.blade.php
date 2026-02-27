<div class="card">
    <div class="card-header ">
        <x-session-flash />
        <div class="row">
            <div class="col-md-3">
                <h5 class="mb-0">Transactions Report</h5>
            </div>
            <div class="col-md-9 d-flex justify-content-end align-items-center">
                <div class="me-4">
                    <select wire:model.change="transactionStatus" class="form-select">
                        <option value="">All Transactions</option>
                        <option value="Completed">Completed</option>
                        <option value="Failed">Failed</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <x-date-filter />
                <x-export-buttons />
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th class="text-end">Phone Number</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Transaction ID</th>
                        <th>Payment Reference</th>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Transaction Date</th>
                    </tr>
                </thead>
                <tbody class="">
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $record->customer->name }}</td>
                            <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                            <td>{{ $record->Status }}</td>
                            <td>{{ $record->Type }}</td>
                            <td>{{ $record->Provider_TXN_ID }}</td>
                            <td>{{ $record->Payment_Reference }}</td>
                            <td>{{ $record->Narration }}</td>
                            <td class="text-end"><x-money :value="$record->Amount" /></td>
                            <td class="text-end">{{ $record->created_at->toDayDateTimeString() }}</td>

                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="fw-bold">

                    <tr>
                        <th>Totals</th>
                        <th class="text-end">{{ $records->count() }}</th>
                        <th class="text-end"><x-money :value="$records->sum('Amount')" /></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="pagination mt-5 d-flex justify-content-end">
            {{ $records->links() }}
        </div>
    </div>
</div>
