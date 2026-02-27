<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text"
                           class="form-control"
                           placeholder="Search by customer name or phone number..."
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>
            <div class="col-md-9 d-flex justify-content-end align-items-center">
                <div class="me-3">
                    <select class="form-select py-1 px-2" wire:model.live="loanProductId">
                        @foreach($loanProducts as $loanProductId => $loanProductName)
                            <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                        @endforeach
                    </select>
                </div>
                <x-date-filter/>
                <x-export-buttons/>
            </div>
        </div>
    </div>

    <div class="card-body">
        <x-session-flash/>

        <div class="table-responsive overflow-x-scroll">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th class="text-end">Payment Reference</th>
                        <th>Customer Name</th>
                        <th class="text-end">Telephone Number</th>
                        <th class="text-end">Date</th>
                        <th>Account Name</th>
                        <th class="text-end">DR</th>
                        <th class="text-end">CR</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="8">Opening Balance</th>
                        <th class="text-end">{{ number_format(data_get($summary, 'opening_balance', 0))  }}</th>
                    </tr>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record['loan_id'] }}</td>
                            <td class="text-end">{{ $record['payment_reference'] }}</td>
                            <td>{{ $record['customer_name'] }}</td>
                            <td class="text-end">{{ $record['telephone_number'] }}</td>
                            <td class="text-end">
                                {{ \Carbon\Carbon::parse($record['created_at'])->format('Y-m-d H:i') }}
                            </td>
                            <td>{{ $record['account_name'] }}</td>
                            <td class="text-end">
                                @if($record['debit_amount'] > 0)
                                    {{ number_format($record['debit_amount'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end">
                                @if($record['credit_amount'] > 0)
                                    {{ number_format($record['credit_amount'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="">
                                    {{ number_format(abs($record['balance']), 2) }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No journal entries found for the selected criteria.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($records->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th class="text-end">{{ $records->count() }} entries</th>
                            <th colspan="4"></th>
                            <th class="text-end">{{ number_format($records->sum('debit_amount'), 2) }}</th>
                            <th class="text-end">{{ number_format($records->sum('credit_amount'), 2) }}</th>
                            <th class="text-end">
                                <span class="">
                                    {{ number_format($records->last()['balance'], 2) }}
                                </span>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-start" colspan="8">Closing Balance</th>
                            <th class="text-end">{{ number_format(data_get($summary, 'closing_balance', 0), 2) }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    @if($records->hasPages())
        <div class="card-footer">
            {{ $records->links() }}
        </div>
    @endif
</div>
