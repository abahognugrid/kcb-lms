<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">External Accounts Report</h5>
            </div>
            <div class="col-md-8 d-flex justify-content-end gap-2">
                <select class="form-select w-50" wire:model.live="serviceProvider">
                    <option value="">All Service Providers</option>
                    @foreach ($serviceProviders as $provider)
                        <option value="{{ $provider }}">{{ $provider }}</option>
                    @endforeach
                </select>
                <x-date-filter/>
                <x-export-buttons/>
            </div>
        </div>
    </div>

    <div class="card-body">
        <table id="report-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-start">Partner</th>
                    <th class="text-start">Service Provider</th>
                    <th class="text-end">Disbursement Account</th>
                    <th class="text-end">Collection Account</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->created_at->toDateString() }}</td>
                    <td>{{ $record->partner->Institution_Name }}</td>
                    <td>{{ $record->service_provider }}</td>
                    <td class="text-end"><x-money :value="$record->disbursement_account" /></td>
                    <td class="text-end"><x-money :value="$record->collection_account" /></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No external account balances found</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr>
                <th class="text-start">Totals</th>
                <th class="text-end">{{ $records->count() }} records</th>
                <th></th>
                <th class="text-end"><x-money :value="$records->sum('disbursement_account')" /></th>
                <th class="text-end"><x-money :value="$records->sum('collection_account')" /></th>
            </tr>
            </tfoot>
        </table>

        <div class="mt-4">
            {{ $records->links() }}
        </div>
    </div>
</div>
