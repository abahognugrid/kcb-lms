@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ data_get($filters, 'partnerName') }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Loan Loss Provisions Report</h4>
        <p style="margin-top: 0; font-size: 10px">Loan Product: {{ data_get($filters, 'loanProductName') }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th>Classification</th>
                <th class="text-end" style="width: 200px">Days</th>
                <th class="text-end">Arrears Amount</th>
                <th class="text-end">Suspended Interest</th>
                <th class="text-end">Provision %ge</th>
                <th class="text-end">Provision Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>{{ $record->ageing_category }}</td>
                    <td class="text-end">{{ $record->days }}</td>
                    <td class="text-end">{{ number_format($record->arrears_amount) }}</td>
                    <td class="text-end">{{ number_format($record->suspended_interest) }}</td>
                    <td class="text-end">{{ round($record->provision_rate) }}</td>
                    <td class="text-end">{{ number_format($record->provision_amount) }}</td>
                </tr>
            @endforeach
            <tr>
                <th class="fw-bold">Total</th>
                <th></th>
                <th class="fw-bold text-end">{{ number_format($records->sum('arrears_amount')) }}</th>
                <th class="fw-bold text-end">{{ number_format($records->sum('suspended_interest')) }}</th>
                <th></th>
                <th class="fw-bold text-end">{{ number_format($records->sum('provision_amount')) }}</th>
            </tr>
        </tbody>
    </table>
    <x-print-footer />
@endsection
