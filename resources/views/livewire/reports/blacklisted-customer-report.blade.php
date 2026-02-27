<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">Black Listed Report</h5>
            </div>
            <div class="col-md-8 d-flex justify-content-end">
                <x-date-filter />
                <x-export-buttons />
            </div>
        </div>
    </div>

    <div class="card-body">
        <x-session-flash />
        <table id="report-table" class="table table-bordered">
            <thead>
                <tr class="table-header">
                    <th class="text-start">Customer #</th>
                    <th class="text-start">Customer Name</th>
                    <th class="text-end">Phone Number</th>
                    <th class="text-start">Amount Disbursed</th>
                    <th class="text-start">Amount Repaid</th>
                    <th class="text-end">Date Blacklisted</th>
                    <th>Reason</th>
                    <th>Blacklisted By</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record->customer_id }}</td>
                        <td>{{ $record->customer_name }}</td>
                        <td class="text-end">{{ $record->telephone_number }}</td>
                        <td class="text-end"><x-money :value="$record->amount_disbursed" /></td>
                        <td class="text-end"><x-money :value="$record->amount_repaid" /></td>
                        <td class="text-end">{{ $record->date_blacklisted }}</td>
                        <td class="">{{ $record->reason_for_blacklisting }}</td>
                        <td class="">{{ $record->blacklisted_by_name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No black listed customers found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-start">Totals</th>
                    <th class="text-end">{{ $records->count() }}</th>
                    <th></th>
                    <th class="text-end"><x-money :value="$records->sum('amount_disbursed')" /></th>
                    <th class="text-end"><x-money :value="$records->sum('amount_repaid')" /></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer pagination mt-5 d-flex justify-content-end">
        {{ $records->links() }}
    </div>
</div>
