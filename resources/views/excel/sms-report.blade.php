<table id="report-table" class="table table-bordered">
    <thead>
    <tr>
        <th colspan="4" style="font-size: 20px; text-align: center; padding: 2px;">{{ data_get($filters, 'partnerName') }}</th>
    </tr>
    <tr>
        <th colspan="4" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Sms Report</th>
    </tr>
    <tr>
        <th colspan="4" style="font-weight: bold; font-size: 14px; text-align: center; padding: 2px;">From: {{ $filters['startDate']  }} to {{ $filters['endDate'] }}</th>
    </tr>
    <tr>
        <th colspan="4"></th>
    </tr>
    <tr class="table-header">
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
<x-print-footer/>

