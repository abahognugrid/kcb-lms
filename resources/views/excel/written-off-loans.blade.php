<table>
    <thead>
        <tr>
            <th colspan="{{ $showRecoveries ? 10 : 8 }}" style="text-align: center; font-weight: bold; font-size: 18px;">
                {{ data_get($filters, 'partnerName') }}</th>
        </tr>
        <tr>
            <th colspan="{{ $showRecoveries ? 10 : 8 }}" style="text-align: center; font-weight: bold; font-size: 14px;">
                Written Off Loans Report</th>
        </tr>
        <tr>
            <th colspan="{{ $showRecoveries ? 10 : 8 }}" style="text-align: center; font-weight: bold; font-size: 10px;">
                Period: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th colspan="{{ $showRecoveries ? 10 : 8 }}"></th>
        </tr>
        <tr>
            <th style="text-align: left; font-weight: bold; width: 80px">Loan #</th>
            <th style="text-align: left; font-weight: bold; width: 180px">Customer Name</th>
            <th style="text-align: right; font-weight: bold; width: 150px">Phone Number</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Amount Disbursed</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Amount Written Off</th>
            <th style="text-align: right; font-weight: bold; width: 100px">Date Written Off</th>
            @if ($showRecoveries)
                <th style="text-align: right; font-weight: bold; width: 100px">Amount Recovered</th>
                <th style="text-align: right; font-weight: bold; width: 100px">Balance After Recovery</th>
                <th style="text-align: right; font-weight: bold; width: 100px">Date Last Recovered</th>
            @endif
            <th style="text-align: right; font-weight: bold; width: 100px">Written Off By</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->customer->name }}</td>
                <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                <td class="text-end">{{ $record->Facility_Amount_Granted }}</td>
                <td class="text-end">{{ $record->Written_Off_Amount }}</td>
                <td class="text-end">{{ $record->Written_Off_Date->format('d-m-Y') }}</td>
                @if ($showRecoveries)
                    <td class="text-end">{{ $record->Written_Off_Amount_Recovered }}</td>
                    <td class="text-end">
                        {{ $record->schedule_sum_principal_remaining - $record->Written_Off_Amount_Recovered }}</td>
                    <td class="text-end">{{ $record->Last_Recovered_At }}</td>
                @endif
                <td>{{ $record->writtenOffBy?->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
