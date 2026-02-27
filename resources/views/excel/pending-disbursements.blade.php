<table>
    <thead>
        <tr>
            <th colspan="6">Pending Disbursement Report</th>
        </tr>
        <tr>
            <th colspan="6">{{ $partnerName }}</th>
        </tr>
        <tr>
            <th colspan="6">Period: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</th>
        </tr>
        <tr>
            <th>Loan ID</th>
            <th>Customer Name</th>
            <th>Product</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Application Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
            <tr>
                <td>{{ $record->loan_id }}</td>
                <td>{{ $record->customer_name }}</td>
                <td>{{ $record->product }}</td>
                <td>{{ number_format($record->amount, 2) }}</td>
                <td>{{ $record->status }}</td>
                <td>{{ $record->application_date }}</td>
            </tr>
        @endforeach
    </tbody>
</table>