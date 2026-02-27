<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Income Report</h5>
        <div class="d-flex justify-content-end">
            <select class="form-select w-25 mx-3" wire:model.live="loanProductId" name="loanProductId">
                <option value="">Select Loan Product</option>
                @foreach ($loanProducts as $loanProductId => $loanProductName)
                    <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                @endforeach
            </select>
            <x-date-filter/>
            <x-export-buttons/>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Account</th>
                <th class="text-end">Balance</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($records as $entry)
                <tr>
                    <td>{{ $entry->name }}</td>
                    <td class="text-end"><x-money :value="$entry->amount" /></td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No records found</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
