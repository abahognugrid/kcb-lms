<div>
    <!-- Date Filters -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-end gap-2 my-4">
                <x-date-filter/>
                <x-export-buttons/>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
            <tr>
                <th>ID #</th>
                <th>Transaction Date</th>
                <th>Txn ID</th>
                <th>Type</th>
                <th class="text-end">Principal</th>
                <th class="text-end">Interest</th>
                <th class="text-end">Fees</th>
                <th class="text-end">Penalty</th>
                <th class="text-end">Total Paid</th>
                <th class="text-end">Balance Due</th>
                <th class="text-end">Principal Balance</th>
                <th class="text-end">Interest Balance</th>
                <th class="text-end">Fees Balance</th>
                <th class="text-end">Penalty Balance</th>
                <th class="text-end">Total Balance</th>
            </tr>
            </thead>
            <tbody>

            @forelse($records as $index => $entry)
                <tr>
                    <td>{{ $entry['loan_id'] }}</td>
                    <td>{{ $entry['transaction_date'] }}</td>
                    <td>{{ $entry['txn_id'] }}</td>
                    <td>{{ $entry['type'] }}</td>
                    <td class="text-end">{{ number_format($entry['principal'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['interest'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['fees'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['penalty'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['total_paid'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['balance_due'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['principal_balance'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['interest_balance'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['fees_balance'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['penalty_balance'], 2) }}</td>
                    <td class="text-end">{{ number_format($entry['total_balance'], 2) }}</td>
                </tr>@empty
                <tr>
                    <td class="text-center" colspan="15">No records found</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="card-footer">
        {{ $records->links() }}
    </div>
</div>
