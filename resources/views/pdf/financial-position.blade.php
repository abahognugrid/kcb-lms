@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partner->Institution_Name }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Statement of Financial Position</h4>
        <p style="margin-top: 0; font-size: 10px">As at: {{ $filters['endDate'] }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <th class="text-start">Account</th>
            <th class="text-end">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($records->assets as $entry)
            <tr>
                <td>
                    <span class="badge bg-label-primary">{{ $entry->identifier }}</span>
                    <span class="text-black">{{ $entry->name }}</span>
                </td>
                <td class="text-end">{{ number_format($entry->balance) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th class="text-start">Total Assets</th>
            <th class="text-end">{{ number_format($totalAssets = $records->assets->sum('balance')) }}</th>
        </tr>
        </tfoot>
    </table>

    <h5 class="mt-4">Liabilities</h5>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <th class="text-start">Account</th>
            <th class="text-end">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($records->liabilities as $entry)
            <tr>
                <td>
                    <span class="badge bg-label-primary">{{ $entry->identifier }}</span>
                    <span class="text-black">{{ $entry->name }}</span>
                </td>
                <td class="text-end">{{ number_format($entry->balance) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th class="text-start">Total Liabilities</th>
            <th class="text-end">{{ number_format($totalLiabilities = $records->liabilities->sum('balance')) }}</th>
        </tr>
        </tfoot>
    </table>


    <h5 class="mt-4">Capital</h5>
    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Account</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($records->capital as $entry)
            <tr>
                <td>
                    <span class="badge bg-label-primary">{{ $entry->identifier }}</span>
                    <span class="text-black">{{ $entry->name }}</span>
                </td>
                <td class="text-end">{{ number_format($entry->balance) }}</td>
            </tr>
        @endforeach
            <tr>
                <td>Retained Earnings</td>
                <td class="text-end">{{ number_format($records->retainedEarnings) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start">Total Capital</th>
                <th class="text-end">{{ number_format($totalCapital = $records->capital->sum('balance')) }}</th>
            </tr>
            <tr class="text-start">
                <th class="text-start">Total Liabilities + Capital</th>
                <th class="text-end">{{ number_format($totalCapitalAndEarningsAndLiabilities = $totalLiabilities + $totalCapital + $records->retainedEarnings) }}</th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer/>
@endsection
