<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th colspan="16" style="font-size: 20px; text-align: center; padding: 2px;">{{ data_get($filters, 'partnerName') }}</th>
        </tr>
        <tr>
            <th colspan="16" style="font-weight: bold; font-size: 16px; text-align: center; padding: 2px;">Portfolio At Risk Report</th>
        </tr>
        <tr>
            <th colspan="16" style="font-weight: bold; font-size: 14px; text-align: center; padding: 2px;">As at: {{ data_get($filters, 'endDate') }}</th>
        </tr>
        <tr>
            <th colspan="16"></th>
        </tr>
        <tr>
            <th colspan="6"></th>
            <th colspan="10" style="text-align: center; font-weight: bold;">Principal in Arrears</th>
        </tr>
        <tr>
            <th style="width: 80px; font-weight: bold;">Loan #</th>
            <th style="width: 180px; font-weight: bold;">Name</th>
            <th style="width: 150px; text-align: right; font-weight: bold;">Phone Number</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Amount Disbursed</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Maturity Date</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Principal Outstanding</th>
            <th style="width: 100px; text-align: right; font-weight: bold;">Arrears Principal</th>
            @foreach(data_get($filters, 'ageingDays', []) as $provision)
                <th style="width: 100px; text-align: right; font-weight: bold;">{{ $provision->days }} <br/>days</th>
                <th style="width: 100px; text-align: right; font-weight: bold;">PAR</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    @forelse($loans as $loan)
        <tr>
            <td class="text-nowrap">{{ $loan->id }}</td>
            <td class="text-nowrap">{{ $loan->customer->name }}</td>
            <td style="text-align: right;">{{ $loan->customer->Telephone_Number }}</td>
            <td style="text-align: right;">{{$loan->Facility_Amount_Granted}}</td>
            <td style="text-align: right;">{{ $loan->Maturity_Date->format('d-m-Y') }}</td>
            <td style="text-align: right;">{{$loan->schedule_sum_principal_remaining}}</td>
            <td style="text-align: right;">{{$loan->principal_in_arrears}}</td>
            <td style="text-align: right;">{{$loan->principal_outstanding_at_30}}</td>
            <td style="text-align: right;">{{ percentage($loan->principal_outstanding_at_30, $loan->schedule_sum_principal_remaining) }}</td>
            <td style="text-align: right;">{{$loan->principal_outstanding_at_60}}</td>
            <td style="text-align: right;">{{ percentage($loan->principal_outstanding_at_60, $loan->schedule_sum_principal_remaining) }}</td>
            <td style="text-align: right;">{{$loan->principal_outstanding_at_90}}</td>
            <td style="text-align: right;">{{ percentage($loan->principal_outstanding_at_90, $loan->schedule_sum_principal_remaining) }}</td>
            <td style="text-align: right;">{{$loan->principal_outstanding_at_180}}</td>
            <td style="text-align: right;">{{ percentage($loan->principal_outstanding_at_180, $loan->schedule_sum_principal_remaining) }}</td>
            <td style="text-align: right;">{{$loan->principal_outstanding_after_180}}</td>
            <td style="text-align: right;">{{ percentage($loan->principal_outstanding_after_180, $loan->schedule_sum_principal_remaining) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="17">No records found</td>
        </tr>
    @endforelse
    </tbody>
    <tfoot>
    @php
        $principal = $loans->sum('principal_outstanding')
    @endphp
    <tr>
        <th class="text-nowrap">Totals</th>
        <th class="text-end">{{ $loans->count() }}</th>
        <th class="text-end"></th>
        <th class="text-end">{{$loans->sum('Facility_Amount_Granted')}}</th>
        <th class="text-end"></th>
        <th class="text-end">{{$principal}}</th>
        <th class="text-end">{{$loans->sum('principal_in_arrears')}}</th>
        <th class="text-end">{{$totalAt30 = $loans->sum('principal_outstanding_at_30')}}</th>
        <th class="text-end">{{ percentage($totalAt30, $principal) }}%</th>
        <th class="text-end">{{$totalAt60 = $loans->sum('principal_outstanding_at_60')}}</th>
        <th class="text-end">{{ percentage($totalAt60, $principal) }}%</th>
        <th class="text-end">{{$totalAt90 = $loans->sum('principal_outstanding_at_90')}}</th>
        <th class="text-end">{{ percentage($totalAt90, $principal) }}%</th>
        <th class="text-end">{{$totalAt180 = $loans->sum('principal_outstanding_at_180')}}</th>
        <th class="text-end">{{ percentage($totalAt180, $principal) }}%</th>
        <th class="text-end">{{$totalAfter180 = $loans->sum('principal_outstanding_after_180')}}</th>
        <th class="text-end">{{ percentage($totalAfter180, $principal) }}%</th>
    </tr>
    <tr>
        <td colspan="16"></td>
    </tr>
    <tr>
        <td colspan="16"><x-print-footer/></td>
    </tr>
    </tfoot>
</table>

