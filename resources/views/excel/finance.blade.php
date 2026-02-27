<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Finance Report</title>
</head>

<body>
    <table>
        <tbody>
            <tr>
                <td colspan="2">
                    <strong>Finance Report</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2">Report Date: {{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>

            @if ($partner_id && $partners)
                @php
                    $partner = $partners->firstWhere('id', $partner_id);
                @endphp
                <tr>
                    <td colspan="2">Partner: {{ $partner ? $partner->Institution_Name : 'N/A' }}</td>
                </tr>
            @endif

            <tr></tr>
            <tr>
                <td colspan="2">
                    <strong>Overall Summary</strong>
                </td>
            </tr>
            <tr>
                <th>Category</th>
                <th>Amount (UGX)</th>
            </tr>
            <tr>
                <td>SMS Revenue</td>
                <td style="text-align: right">{{ number_format($smsRevenue, 2) }}</td>
            </tr>
            <tr>
                <td>Bulk SMS Revenue</td>
                <td style="text-align: right">{{ number_format($bulkSmsRevenue, 2) }}</td>
            </tr>
            <tr>
                <td>Total Commission</td>
                <td style="text-align: right">{{ number_format($totalCommission, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
