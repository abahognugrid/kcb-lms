@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loan Reports - Loan Product Report')

@section('content')
    <div class="table-wrapper">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Loan Product Report</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-end">Number of Loans</th>
                                <th class="text-end">Total Principal</th>
                                <th class="text-end">Principal Due</th>
                                <th class="text-end">Interest Due</th>
                                <th class="text-end">Fees Due</th>
                                <th class="text-end">Penalty Due</th>
                                <th class="text-end">Total Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportData as $data)
                                <tr>
                                    <td>{{ $data['name'] }}</td>
                                    <td class="text-end">{{ $data['number_of_loans'] }}</td>
                                    <td class="text-end">{{ $data['total_principal'] }}</td>
                                    <td class="text-end">{{ $data['principal_due'] }}</td>
                                    <td class="text-end">{{ $data['interest_due'] }}</td>
                                    <td class="text-end">{{ $data['fees_due'] }}</td>
                                    <td class="text-end">{{ $data['penalty_due'] }}</td>
                                    <td class="text-end">{{ $data['total_due'] }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td>Totals</td>
                                <td class="text-end">{{ $overallTotals['number_of_loans'] }}</td>
                                <td class="text-end">{{ number_format($overallTotals['total_principal'], 2) }}</td>
                                <td class="text-end">{{ number_format($overallTotals['principal_due'], 2) }}</td>
                                <td class="text-end">{{ number_format($overallTotals['interest_due'], 2) }}</td>
                                <td class="text-end">{{ number_format($overallTotals['fees_due'], 2) }}</td>
                                <td class="text-end">{{ number_format($overallTotals['penalty_due'], 2) }}</td>
                                <td class="text-end">{{ number_format($overallTotals['total_due'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
