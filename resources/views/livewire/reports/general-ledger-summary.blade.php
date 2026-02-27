<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">General Ledger Summary</h5>
        <div class="d-flex justify-content-end">
            <x-date-filter />
            <x-export-buttons />
        </div>
    </div>
    <div class="card-body">
        <p class="alert alert-secondary text-dark">
            If an account has had no transactions (no debit or credit entries), it will generally not appear in the
            General Ledger Summary, as there's no financial activity to report.
        </p>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th class="text-end">Total Debit</th>
                    <th class="text-end">Total Credit</th>
                    <th class="text-end">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $entry)
                    <tr>
                        <td>{{ $entry->name }}</td>
                        <td class="text-end"><x-money :value="$entry->total_debit" /></td>
                        <td class="text-end"><x-money :value="$entry->total_credit" /></td>
                        <td class="text-end">
                            @php
                                $balance = 0;
                                if (in_array($entry->type_letter, ['A', 'E'])) {
                                    $balance = $entry->total_debit - $entry->total_credit;
                                } elseif (in_array($entry->type_letter, ['C', 'I', 'L'])) {
                                    $balance = $entry->total_credit - $entry->total_debit;
                                }
                            @endphp
                            <x-money :value="$balance" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
