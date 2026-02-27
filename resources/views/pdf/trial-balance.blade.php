@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Trial Balance</h4>
        <p style="margin-top: 0; font-size: 10px">As at: {{ $filters['endDate'] }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Account</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($records as $entry)
            <tr>
                <td>
                    <span class="badge bg-label-primary fw-normal">{{ $entry->account->identifier }}</span>
                    <span class="text-black">{{ $entry->account->name }}</span>
                </td>
                <td class="text-end">{{ number_format($entry->debit_amount) }}</td>
                <td class="text-end">{{ number_format($entry->credit_amount) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start">Totals</th>
                <th class="text-end"><x-money :value="$records->sum('debit_amount')" /></th>
                <th class="text-end"><x-money :value="$records->sum('credit_amount')" /></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer/>
@endsection
