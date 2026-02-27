<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Trial Balance</h5>
        <div class="d-flex justify-content-end">
            <x-end-date/>
            <x-export-buttons/>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Account</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($records as $entry)
                <tr>
                    <td>
                        <span class="badge bg-label-primary fw-normal">{{ $entry->account->identifier }}</span>
                        <span class="text-black">{{ $entry->account->name }}</span>
                    </td>
                    <td class="text-end">{{ number_format($entry->debit_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($entry->credit_amount, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>Totals</th>
                <th class="text-end">{{ number_format($records->sum('debit_amount'), 2) }}</th>
                <th class="text-end">{{ number_format($records->sum('credit_amount'), 2) }}</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
