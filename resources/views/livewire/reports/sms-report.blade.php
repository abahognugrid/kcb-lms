<div class="card">
    <div class="card-header ">
        <x-session-flash/>
        <div class="row">
            <div class="col-md-3">
                <h5 class="mb-0">SMS Report</h5>
            </div>
            <div class="col-md-9 d-flex justify-content-end align-items-center">
                <x-date-filter/>
                <x-export-buttons/>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="report-table" class="table table-bordered">
            <thead>
                <tr>
                    <th style="min-width: 250px">Customer Name</th>
                    <th class="text-end">Phone Number</th>
                    <th>Message</th>
                    <th class="text-end" style="min-width: 250px">Date Sent</th>
                </tr>
            </thead>
            <tbody class="">
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->notifiable->name }}</td>
                    <td class="text-end">{{ $record->notifiable->Telephone_Number }}</td>
                    <td><small>{{ data_get($record->data, 'message') }}</small></td>
                    <td class="text-end">{{ $record->created_at->toDateTimeString() }}</td> <!-- todo: Use approved at on loan or statuses -->
                </tr>
            @empty
                <tr>
                    <td colspan="4">No sms found.</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot class="fw-bold">
            <tr>
                <th>Totals</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th colspan="2"></th>
            </tr>
            </tfoot>
        </table>
        <div class="pagination mt-5 d-flex justify-content-end">
            {{ $records->links() }}
        </div>
    </div>
</div>
