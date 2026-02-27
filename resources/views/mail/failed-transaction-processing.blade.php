<!DOCTYPE html>
<html>
<head>
    <title>Unprocessed Transaction Alert</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background: #f4f4f4; padding: 20px; border-bottom: 3px solid #007cba; }
        .content { padding: 20px; }
        .transaction-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .transaction-table th, .transaction-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .transaction-table th { background-color: #f2f2f2; }
        .alert { background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0; }
        .button { display: inline-block; padding: 10px 20px; background: #007cba; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Unprocessed Transaction Alert</h1>
        <p><strong>Partner:</strong> {{ $partner->Institution_Name }}</p>
    </div>

    <div class="content">
        <div class="alert">
            <strong>Alert:</strong> {{ $transactionCount }} transaction(s) failed to process and require manual review.
        </div>

        <h2>Transaction Details</h2>
        <table class="transaction-table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Telephone</th>
                    <th>Type</th>
                    <th>Amount (UGX)</th>
                    <th>Payment Reference</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->customer->name }}</td>
                    <td>{{ $transaction->Telephone_Number }}</td>
                    <td>{{ $transaction->Type }}</td>
                    <td>{{ number_format($transaction->Amount, 2) }}</td>
                    <td>{{ $transaction->Payment_Reference }}</td>
                    <td>{{ $transaction->created_at->toDateTimeString() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Summary:</strong></p>
        <ul>
            <li>Partner: {{ $partner->Institution_Name }}</li>
            <li>Total Failed Transactions: {{ $transactionCount }}</li>
            <li>Alert Time: {{ now()->format('Y-m-d H:i:s') }}</li>
        </ul>

        <p>These transactions require manual review and processing. Please investigate the cause of the failures and take appropriate action.</p>

        <a href="{{ route('transactions.index') }}" class="button">View Transaction Report</a>

        <p>Thanks,<br>{{ config('app.name') }} System</p>
    </div>
</body>
</html>
