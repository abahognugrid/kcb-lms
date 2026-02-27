<div class="table-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-0">Borrowers Report</h5>
                </div>
                <div class="col-md-8 d-flex justify-content-end">
                    <x-date-filter/>
                    <x-export-buttons/>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Customer Name</th>
                            <th class="text-nowrap text-end">Phone Number</th>
                            <th class="text-nowrap text-end">Loans Count</th>
                            <th class="text-nowrap text-end">Amount Borrowed</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td class="text-nowrap">{{ $record->name }}</td>
                            <td class="text-nowrap text-end">{{ $record->Telephone_Number }}</td>
                            <td class="text-nowrap text-end">{{ $record->loans_count }}</td>
                            <td class="text-nowrap text-end">{{ number_format($record->loans_sum_facility__amount__granted) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Totals</th>
                            <th class="text-end">{{ $records->count() }}</th>
                            <th class="text-end">{{ $records->sum('loans_count') }}</th>
                            <th class="text-end">{{ number_format($records->sum('loans_sum_facility__amount__granted')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            {{ $records->links() }}
        </div>
    </div>
</div>
