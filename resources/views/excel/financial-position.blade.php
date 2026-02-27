
    <table class="table table-bordered">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center; font-weight: bold; font-size: 18px">{{ $partnerName }}</th>
            </tr>
            <tr>
                <th colspan="2" style="text-align: center; font-weight: bold; font-size: 14px">Statement of Financial Position</th>
            </tr>
            <tr>
                <th colspan="2" style="text-align: center; font-weight: bold; font-size: 12px">As at: {{ data_get($filters, 'endDate') }}</th>
            </tr>
            <tr>
                <th colspan="2"></th>
            </tr>
            <tr>
                <th colspan="2" style="font-weight: bold">Assets</th>
            </tr>
            <tr class="table-header">
                <th style="width: 300px; text-align: left; font-weight: bold;">Account</th>
                <th style="width: 80px; text-align: right; font-weight: bold;">Amount</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($records->assets as $entry)
            <tr>
                <td>
                    <span class="badge bg-label-primary">{{ $entry->identifier }}</span>
                    <span class="text-black">{{ $entry->name }}</span>
                </td>
                <td class="text-end">{{ $entry->balance }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start" style="font-weight: bold">Total Assets</th>
                <th class="text-end" style="text-align: right; font-weight: bold;">{{ $totalAssets = $records->assets->sum('balance') }}</th>
            </tr>
        </tfoot>
    </table>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th colspan="2" style="font-weight: bold">Liabilities</th>
            </tr>
            <tr class="table-header">
                <th class="text-start" style="font-weight: bold;">Account</th>
                <th class="text-end" style="font-weight: bold; text-align: right">Amount</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($records->liabilities as $entry)
            <tr>
                <td>
                    <span class="badge bg-label-primary">{{ $entry->identifier }}</span>
                    <span class="text-black">{{ $entry->name }}</span>
                </td>
                <td class="text-end">{{ $entry->balance }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th class="text-start" style="text-align: left; font-weight: bold">Total Liabilities</th>
            <th class="text-end" style="text-align: right; font-weight: bold">{{ $totalLiabilities = $records->liabilities->sum('balance') }}</th>
        </tr>
        </tfoot>
    </table>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th colspan="2" style="font-weight: bold">Capital</th>
            </tr>
            <tr class="table-header">
                <th class="text-start" style="font-weight: bold;">Account</th>
                <th class="text-end" style="font-weight: bold; text-align: right">Amount</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($records->capital as $entry)
            <tr>
                <td>
                    <span class="badge bg-label-primary">{{ $entry->identifier }}</span>
                    <span class="text-black">{{ $entry->name }}</span>
                </td>
                <td class="text-end">{{ $entry->balance }}</td>
            </tr>
        @endforeach
            <tr>
                <td>Retained Earnings</td>
                <td class="text-end">{{ $records->retainedEarnings }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start" style="text-align: left; font-weight: bold">Total Capital</th>
                <th class="text-end" style="text-align: right; font-weight: bold">{{ $totalCapital = $records->capital->sum('balance') }}</th>
            </tr>
            <tr class="text-start">
                <th style="font-weight: bold">Total Liabilities + Capital</th>
                <th class="text-end" style="text-align: right; font-weight: bold;">{{ $totalLiabilities + $totalCapital + $records->retainedEarnings }}</th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer/>
