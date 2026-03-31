<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="8" style="font-size: 20px; text-align: center; padding: 2px;">
                {{ data_get($filters, 'partnerName') }}</th>
        </tr>
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Delinked
                Customers Report</th>
        </tr>
        <tr>
            <th colspan="8" style="font-size: 12px; text-align: center; padding-top: 2px;">From:
                {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr class="table-header">
            <th style="text-align: left; font-weight: bold; width: 80px;">Customer #</th>
            <th style="text-align: left; font-weight: bold; width: 180px;">First Name</th>
            <th style="text-align: left; font-weight: bold; width: 180px;">Last Name</th>
            <th style="text-align: right; font-weight: bold; width: 140px;">Phone Number</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Amount Disbursed</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Amount Repaid</th>
            <th style="text-align: right; font-weight: bold; width: 100px;">Delinked At</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->First_Name }}</td>
                <td>{{ $record->Last_Name }}</td>
                <td style="text-align: right">{{ $record->telephone_number }}</td>
                <td style="text-align: right">{{ $record->amount_disbursed }}</td>
                <td style="text-align: right">{{ $record->amount_repaid }}</td>
                <td style="text-align: right">{{ $record->date_delinked }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8">No records found</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th style="font-weight:bold">Totals</th>
            <th style="text-align: right;font-weight:bold">{{ $records->count() }}</th>
            <th></th>
            <th></th>
            <th style="text-align: right;font-weight:bold">{{ $records->sum('amount_disbursed') }}</th>
            <th style="text-align: right;font-weight:bold">{{ $records->sum('amount_repaid') }}</th>
            <th colspan="3"></th>
        </tr>
    </tfoot>
</table>
<x-print-footer />
