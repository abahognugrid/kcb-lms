@extends('pdf.layouts')
@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Transactions Report @if ($status = data_get($filters, 'transactionStatus'))
                - {{ strtoupper($status) }}
            @endif
        </h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table id="report-table" class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Customer Name</th>
                <th class="text-end">Phone Number</th>
                <th class="text-start">Status</th>
                <th class="text-start">Type</th>
                <th class="text-start">Transaction ID</th>
                <th class="text-start">Payment Reference</th>
                <th class="text-start">Description</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Transaction Date</th>
            </tr>
        </thead>
        <tbody class="">
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->customer->name }}</td>
                    <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                    <td>{{ $record->Status }}</td>
                    <td>{{ $record->Type }}</td>
                    <td>{{ $record->Provider_TXN_ID }}</td>
                    <td>{{ $record->Payment_Reference }}</td>
                    <td>{{ $record->Narration }}</td>
                    <td class="text-end"><x-money :value="$record->Amount" /></td>
                    <td class="text-end">{{ $record->created_at->toDayDateTimeString() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="">No deposits found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <th>Totals</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th class="text-end"><x-money :value="$records->sum('Amount')" /></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
