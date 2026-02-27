@extends('pdf.layouts')
@section('content')
    <!-- Header -->
    <div class="text-center">
        <div class="company-name">{{ $partnerName }}</div>
        <div class="report-title">LOAN LEDGER REPORT</div>
    </div>
    <table>
        <tbody>
            <tr>
                <td colspan="15"></td>
            </tr>
            <tr>
                <td colspan="8">
                    <table class="info-table">
                        <tr>
                            <td>Account Reference:</td>
                            <td>{{ $loan->Credit_Account_Reference }}</td>
                        </tr>
                        <tr>
                            <td>Customer Name:</td>
                            <td>{{ $loan->customer->name }}</td>
                        </tr>
                        <tr>
                            <td>Phone Number:</td>
                            <td>{{ $loan->customer->Telephone_Number }}</td>
                        </tr>
                        <tr>
                            <td>Loan Product:</td>
                            <td>{{ $loan->loan_product->name }}</td>
                        </tr>
                    </table>
                </td>
                <td colspan="7">
                    <table class="info-table">
                        <tr>
                            <td>Principal Amount:</td>
                            <td>{{ number_format($loan->Credit_Amount, 2) }} {{ $loan->Currency }}</td>
                        </tr>
                        <tr>
                            <td>Disbursement Date:</td>
                            <td>{{ $loan->Credit_Account_Date?->format('d M Y') ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Maturity Date:</td>
                            <td>{{ $loan->Maturity_Date?->format('d M Y') ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Current Status:</td>
                            <td>{{ \App\Models\Loan::SUPPORTED_Credit_Account_Statuses[$loan->Credit_Account_Status] ?? 'Unknown' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Ledger Table -->
    <table class="table table-hover mb-0">
        <thead class="table-light">
        <tr>
            <td colspan="15"></td>
        </tr>
        <tr>
            <th>ID #</th>
            <th>Transaction Date</th>
            <th>Txn ID</th>
            <th>Type</th>
            <th class="text-end">Principal</th>
            <th class="text-end">Interest</th>
            <th class="text-end">Fees</th>
            <th class="text-end">Penalty</th>
            <th class="text-end">Total Paid</th>
            <th class="text-end">Balance Due</th>
            <th class="text-end">Principal Balance</th>
            <th class="text-end">Interest Balance</th>
            <th class="text-end">Fees Balance</th>
            <th class="text-end">Penalty Balance</th>
            <th class="text-end">Total Balance</th>
        </tr>
        </thead>
        <tbody>

        @forelse($records as $index => $entry)
            <tr>
                <td>{{ $entry['loan_id'] }}</td>
                <td>{{ $entry['transaction_date'] }}</td>
                <td>{{ $entry['txn_id'] }}</td>
                <td>{{ $entry['type'] }}</td>
                <td class="text-end">{{ number_format($entry['principal'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['interest'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['fees'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['penalty'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['total_paid'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['balance_due'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['principal_balance'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['interest_balance'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['fees_balance'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['penalty_balance'], 2) }}</td>
                <td class="text-end">{{ number_format($entry['total_balance'], 2) }}</td>
            </tr>
        @empty
            <tr>
                <td class="text-center" colspan="15">No records found</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endsection
