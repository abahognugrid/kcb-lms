<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-3">
                <h5 class="mb-0">Credit Limits Report</h5>
            </div>
            <div class="col-md-9 d-flex justify-content-end align-items-center">
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
                    <th>Customer Name</th>
                    <th class="text-end">Phone Number</th>
                    <th class="text-end">Total Loan Count</th>
                    <th class="text-end">Total Loan Amount</th>
                    <th class="text-end">Total Outstanding Balance</th>
                    <th class="text-end">Credit Limit</th>
                    <th class="text-end">Used Limit</th>
                    <th class="text-end">Available Credit Limit</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record->customer->name }}</td>
                        <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                        <td class="text-end">{{ $record->totalLoanCount() }}</td>
                        <td class="text-end">{{ 'UGX ' . number_format($record->totalLoanAmount()) }}</td>
                        <td class="text-end">{{ 'UGX ' . number_format($record->totalOutstandingBalance()) }}</td>
                        <td class="text-end">{{ 'UGX ' . number_format($record->credit_limit) }}</td>
                        <td class="text-end">{{ 'UGX ' . number_format($record->used_credit) }}</td>
                        <td class="text-end">{{ 'UGX ' . number_format($record->available_credit) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $records->links() }}
        </div>
    </div>
</div>
