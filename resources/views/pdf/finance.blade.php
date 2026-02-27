<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Finance Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .no-border {
            border: none;
        }

        h2,
        h3 {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <h2>Finance Report</h2>

    <table class="no-border">
        <tr>
            <td class="no-border"><strong>Report Date:</strong></td>
            <td class="no-border">{{ now()->format('Y-m-d H:i:s') }}</td>
        </tr>
        @if ($partner_id && $partners)
            @php
                $partner = $partners->firstWhere('id', $partner_id);
            @endphp
            <tr>
                <td class="no-border"><strong>Partner:</strong></td>
                <td class="no-border">{{ $partner ? $partner->Institution_Name : 'N/A' }}</td>
            </tr>
        @endif
        <tr>
            <td class="no-border"><strong>Filter:</strong></td>
            <td class="no-border">{{ ucfirst(str_replace('_', ' ', $filter)) }}</td>
        </tr>
        @if ($filter === 'custom')
            <tr>
                <td class="no-border"><strong>Date Range:</strong></td>
                <td class="no-border">From {{ $from }} To {{ $to }}</td>
            </tr>
        @endif
    </table>

    <h3>Overall Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Amount (UGX)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>SMS Revenue</td>
                <td>{{ number_format($smsRevenue, 2) }}</td>
            </tr>
            <tr>
                <td>Bulk SMS Revenue</td>
                <td>{{ number_format($bulkSmsRevenue, 2) }}</td>
            </tr>
            <tr>
                <td>Total Commission</td>
                <td>{{ number_format($totalCommission, 2) }}</td>
            </tr>
        </tbody>
    </table>

</body>

</html>
