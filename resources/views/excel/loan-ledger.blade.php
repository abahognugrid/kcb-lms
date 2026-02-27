<table>
    <!-- Report Header -->
    <tr>
        <th colspan="15" style="font-weight: bold; font-size: 16px; text-align: center;">
            {{ $partnerName }}
        </th>
    </tr>
    <tr>
        <th colspan="15" style="font-weight: bold; font-size: 14px; text-align: center;">
            Loan Ledger Report
        </th>
    </tr>
    <tr>
        <th colspan="15" style="font-size: 12px; text-align: center;">
            Period: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}
        </th>
    </tr>
    <tr>
        <th colspan="15"></th>
    </tr>

    @if($loan)
        <!-- Loan Information Section -->
        <tr>
            <th colspan="8" style="font-weight: bold; background-color: #f5f5f5;">Loan Information</th>
            <th colspan="7" style="font-weight: bold; background-color: #f5f5f5;">Loan Summary</th>
        </tr>
        <tr>
            <td colspan="2">Account Reference:</td>
            <td colspan="2">{{ $loan->Credit_Account_Reference }}</td>
            <td colspan="4"></td>
            <td colspan="2">Principal Amount:</td>
            <td colspan="2">{{ $loan->Credit_Amount }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="2">Customer:</td>
            <td colspan="2">{{ $loan->customer->name }}</td>
            <td colspan="4"></td>
            <td colspan="2">Disbursement Date:</td>
            <td colspan="2">{{ $loan->Credit_Account_Date?->format('d M Y') ?? 'N/A' }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="2">Telephone Number:</td>
            <td colspan="2" style="text-align: left;">{{ $loan->customer->Telephone_Number }}</td>
            <td colspan="4"></td>
            <td colspan="2">Maturity Date:</td>
            <td colspan="2">{{ $loan->Maturity_Date?->format('d M Y') ?? 'N/A' }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="2">Loan Product:</td>
            <td colspan="2">{{ $loan->loan_product->Name ?? 'N/A' }}</td>
            <td colspan="4"></td>
            <td colspan="2">Status:</td>
            <td colspan="2">{{ \App\Enums\LoanAccountType::formattedName($loan->Credit_Account_Status) }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <th colspan="15"></th>
        </tr>
    @endif

    <tr>
        <th style="width: 80px;">ID #</th>
        <th style="width: 120px;">Transaction Date</th>
        <th style="width: 100px;">Txn ID</th>
        <th style="width: 120px;">Type</th>
        <th style="width: 100px; text-align: right;">Principal</th>
        <th style="width: 80px; text-align: right;">Interest</th>
        <th style="width: 80px; text-align: right;">Fees</th>
        <th style="width: 80px; text-align: right;">Penalty</th>
        <th style="width: 120px; text-align: right;">Total Paid</th>
        <th style="width: 150px; text-align: right;">Balance Due</th>
        <th style="width: 150px; text-align: right;">Principal Balance</th>
        <th style="width: 150px; text-align: right;">Interest Balance</th>
        <th style="width: 150px; text-align: right;">Fees Balance</th>
        <th style="width: 150px; text-align: right;">Penalty Balance</th>
        <th style="width: 150px; text-align: right;">Total Balance</th>
    </tr>

    @forelse($records as $index => $entry)
        <tr>
            <td>{{ $entry['loan_id'] }}</td>
            <td>{{ $entry['transaction_date'] }}</td>
            <td>{{ $entry['txn_id'] }}</td>
            <td>{{ $entry['type'] }}</td>
            <td style="text-align: right">{{ number_format($entry['principal'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['interest'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['fees'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['penalty'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['total_paid'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['balance_due'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['principal_balance'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['interest_balance'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['fees_balance'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['penalty_balance'], 2, '.', '') }}</td>
            <td style="text-align: right">{{ number_format($entry['total_balance'], 2, '.', '') }}</td>
        </tr>
    @empty
        <tr>
            <td class="text-center" colspan="15">No records found</td>
        </tr>
    @endforelse
</table>
