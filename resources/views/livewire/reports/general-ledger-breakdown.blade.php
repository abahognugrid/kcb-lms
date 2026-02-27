<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="">
            <h5 class="mb-0">GL Statement Break Down Report</h5>
        </div>
        <div class="text-end d-flex justify-content-end">
            <select class="form-select-sm me-2" wire:model.change="accountId">
                <option value="">Choose Account</option>
                @foreach ($accounts as $accountId => $accountName)
                    <option value="{{ $accountId }}">{{ $accountName }}</option>
                @endforeach
            </select>
            <x-date-filter />
            <x-export-buttons />
        </div>
    </div>
    <div class="card-body">
        <table id="report-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID#</th>
                    <th class="">Account</th>
                    <th class="">Customer</th>
                    <th class="">Phone Number</th>
                    <th class="text-end">Entry Date</th>
                    <th class="text-end">DR</th>
                    <th class="text-end">CR</th>
                    <th class="text-end">Balance</th>
                </tr>
                <tr>
                    <th colspan="7">Opening Balance</th>
                    <th class="text-end">{{ number_format(data_get($summary, 'opening_balance'), 2) }}</th>
                </tr>
            </thead>
            <tbody>
                @php $runningBalance = data_get($summary, 'opening_balance', 0); $accountType = $records->first()?->account?->type_letter; @endphp

                @forelse ($records as $key => $record)
                    @if (in_array($accountType, ['A', 'E']))
                        @php $runningBalance += $record->debit_amount; @endphp
                        @php $runningBalance -= $record->credit_amount @endphp
                    @else
                        @php $runningBalance += $record->credit_amount; @endphp
                        @php $runningBalance -= $record->debit_amount @endphp
                    @endif
                    <tr>
                        <td class="">{{ $record->txn_id }}</td>
                        <td class="">{{ $record->account_name }}</td>
                        <td class="">{{ $record->customer?->name }}</td>
                        <td class="">{{ $record->customer?->Telephone_Number }}</td>
                        <td class="text-end">{{ $record->created_at }}</td>
                        <td class="text-end">{{ number_format($record->debit_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($record->credit_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($runningBalance, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No records found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7">Closing Balance</th>
                    <th class="text-end">{{ number_format(data_get($summary, 'closing_balance'), 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
