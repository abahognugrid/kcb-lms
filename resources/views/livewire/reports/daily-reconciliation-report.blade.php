<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-3">
                <h5 class="mb-0">Daily Reconciliation Report</h5>
            </div>
            <div class="col-md-9 d-flex justify-content-end align-items-center">
                <div class="me-3">
                    <select class="form-select py-1 px-2" wire:model.change="loanProductId">
                        @foreach($loanProducts as $loanProductId => $loanProductName)
                            <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="me-3">
                    <select class="form-select py-1 px-2" wire:model.change="accountType">
                        <option value="{{ \App\Services\Account\AccountSeederService::COLLECTION_OVA_SLUG }}">Collection Account</option>
                        <option value="{{ \App\Services\Account\AccountSeederService::DISBURSEMENT_OVA_SLUG }}">Disbursement Account</option>
                    </select>
                </div>
                <x-date-filter/>
                <x-export-buttons/>
            </div>
        </div>
    </div>

    <div class="card-body">
        <x-session-flash/>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Loan #</th>
                    <th>Payment Reference</th>
                    <th>Customer Name</th>
                    <th class="text-end">Phone Number</th>
                    <th class="text-end">Transaction Date</th>
                    @if(! $isDisbursement)
                        <th class="text-end">Principal</th>
                        <th class="text-end">Interest</th>
                        <th class="text-end">Fees</th>
                        <th class="text-end">Penalty</th>
                    @endif
                    <th class="text-end">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="{{ $isDisbursement ? 5 : 9 }}">Opening Balance</th>
                    <th class="text-end">{{ number_format(data_get($summary, 'opening_balance', 0))  }}</th>
                </tr>
                @forelse($records as $record)
                    <tr>
                        <td>{{ $record['loan_id'] }}</td>
                        <td>{{ $record['payment_reference'] }}</td>
                        <td>{{ $record['customer_name'] }}</td>
                        <td class="text-end">{{ $record['transaction']->customer?->Telephone_Number }}</td>
                        <td class="text-end">{{ \Carbon\Carbon::parse($record['transaction_date'])->toDateTimeString() }}</td>
                        @if(! $isDisbursement)
                        <td class="text-end">{{ number_format($record['principal_amount']) }}</td>
                        <td class="text-end">{{ number_format($record['interest_amount']) }}</td>
                        <td class="text-end">{{ number_format($record['fees_amount']) }}</td>
                        <td class="text-end">{{ number_format($record['penalty_amount']) }}</td>
                        @endif
                        <td class="text-end">{{ number_format($record['total_amount']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isDisbursement ? 6 : 10 }}">No records found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td>Sub Total</td>
                    <td></td>
                    <td>{{ $records->count() }}</td>
                    <td colspan="2"></td>
                    @if(! $isDisbursement)
                        <td class="text-end">{{ number_format($records->sum('principal_amount')) }}</td>
                        <td class="text-end">{{ number_format($records->sum('interest_amount')) }}</td>
                        <td class="text-end">{{ number_format($records->sum('fees_amount')) }}</td>
                        <td class="text-end">{{ number_format($records->sum('penalty_amount')) }}</td>
                    @endif
                    <td class="text-end">{{ number_format($records->sum('total_amount')) }}</td>
                </tr>
                <tr>
                    <th colspan="{{ $isDisbursement ? 5 : 9 }}">Closing Balance</th>
                    <th class="text-end">{{ number_format(data_get($summary, 'closing_balance', 0))  }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer">
        {{ $records->links() }}
    </div>
</div>
