<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-weight: bold; font-size: 18px;">{{ data_get($filters, 'partnerName') }}</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; font-weight: bold; font-size: 14px;">Loan Loss Provisions Report
            </th>
        </tr>
        <tr>
            <th colspan="6"></th>
        </tr>
        <tr>
            <th style="text-align: left; font-weight: bold; width: 180px">Classification</th>
            <th style="font-weight: bold; width: 150px">Days</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Arrears Amount</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Suspended Interest</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Provision %ge</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Provision Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->ageing_category }}</td>
                <td>{{ $record->days }}</td>
                <td style="text-align: right;">{{ $record->arrears_amount }}</td>
                <td style="text-align: right;">{{ $record->suspended_interest }}</td>
                <td style="text-align: right;">{{ round($record->provision_rate) }}</td>
                <td style="text-align: right;">{{ $record->provision_amount }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td style="font-weight: bold;">Total</td>
            <td></td>
            <td style="text-align: right; font-weight: bold;">{{ $records->sum('arrears_amount') }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $records->sum('suspended_interest') }}</td>
            <td></td>
            <td style="text-align: right; font-weight: bold;">{{ $records->sum('provision_amount') }}</td>
        </tr>
    </tfoot>
</table>
