@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Other Reports - Fees Report')

@section('content')
    <div class="table-wrapper">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Fees Report</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-end">Total Fees Due</th>
                                <th class="text-end">Total Fees Paid</th>
                                <th class="text-end">Total Fees Pending</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-end">{{ number_format($totalFeesDue, 2) }}</td>
                                <td class="text-end">{{ number_format($totalFeesPaid, 2) }}</td>
                                <td class="text-end">{{ number_format($totalFeesDue - $totalFeesPaid, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 class="mt-4">Detailed Report</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Loan #</th>
                                <th>Disbursement Date</th>
                                <th class="text-end">Principal Amount</th>
                                <th class="text-end">Total Fees Due</th>
                                <th class="text-end">Total Fees Paid</th>
                                <th class="text-end">Total Fees Pending</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportData as $data)
                                <tr>
                                    <td>{{ $data['customer_name'] }}</td>
                                    <td>{{ $data['loan_number'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data['disbursement_date'])->format('Y-m-d') }}</td>
                                    <td class="text-end">{{ number_format($data['principal_amount'], 2) }}</td>
                                    <td class="text-end">{{ $data['total_fees_due'] }}</td>
                                    <td class="text-end">{{ $data['total_fees_paid'] }}</td>
                                    <td class="text-end">{{ $data['total_fees_pending'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
