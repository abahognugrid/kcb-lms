<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Cash Flow Statement</h4>
            <div class="d-flex gap-2">
                <x-end-date/>
                <x-export-buttons/>
            </div>
        </div>
        <div class="card-body">
            <x-session-flash/>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction Type</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($journalEntries as $entry)
                        <tr>
                            <td>{{ $entry->created_at->format('Y-m-d') }}</td>
                            <td>{{ ucfirst($entry->cash_type ?? '-') }}</td>
                            <td class="text-end"><x-money :value="$entry->amount" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $journalEntries->links() }}
        </div>
    </div>
</div>
