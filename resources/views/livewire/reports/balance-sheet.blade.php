<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Statement of Financial Position</h5>
        <div class="d-flex justify-content-end">
            <x-end-date/>
            <x-export-buttons/>
        </div>
    </div>
    <div class="card-body">
        <h5 class="mt-4">Assets</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Account</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($records->assets as $entry)
                <tr>
                    <td>
                        <span class="badge bg-label-primary">{{ $entry->identifier }}</span>
                        <span class="text-black">{{ $entry->name }}</span>
                    </td>
                    <td class="text-end">{{ number_format($entry->balance) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>Total Assets</th>
                <th class="text-end">{{ number_format($totalAssets = $records->assets->sum('balance')) }}</th>
            </tr>
            </tfoot>
        </table>

        <h5 class="mt-4">Liabilities</h5>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Account</th>
                <th class="text-end">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($records->liabilities as $entry)
                <tr>
                    <td>
                        <span class="badge bg-label-primary">{{ $entry->identifier }}</span>
                        <span class="text-black">{{ $entry->name }}</span>
                    </td>
                    <td class="text-end">{{ number_format($entry->balance) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>Total Liabilities</th>
                <th class="text-end">{{ number_format($totalLiabilities = $records->liabilities->sum('balance')) }}</th>
            </tr>
            </tfoot>
        </table>


        <h5 class="mt-4">Capital</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Account</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($records->capital as $entry)
                <tr>
                    <td>
                        <span class="badge bg-label-primary">{{ $entry->identifier }}</span>
                        <span class="text-black">{{ $entry->name }}</span>
                    </td>
                    <td class="text-end">{{ number_format($entry->balance) }}</td>
                </tr>
            @endforeach
                <tr>
                    <td>Retained Earnings</td>
                    <td class="text-end">{{ number_format($records->retainedEarnings) }}</td>
                </tr>
            </tbody>
            <tfoot>
            <tr>
                <th>Total Capital</th>
                <td class="text-end">{{ number_format($totalCapital = $records->capital->sum('balance')) }}</td>
            </tr>
            <tr>
                <th>Total Liabilities + Capital</th>
                <th class="text-end">{{ number_format($totalCapitalAndEarningsAndLiabilities = $totalLiabilities + $totalCapital + $records->retainedEarnings) }}</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
