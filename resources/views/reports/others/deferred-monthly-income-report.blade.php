<!-- resources/views/reports/others/deferred-monthly-income-report.blade.php -->

@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Other Reports - Deferred Monthly Income Report')

@section('content')
    <div class="table-wrapper">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('reports.others.deferred-monthly-income-report') }}" method="GET">
                    <div class="form-group">
                        <label for="loan_product_id">Select Loan Product:</label>
                        <select id="loan_product_id" name="loan_product_id" class="form-control">
                            <option value="">All</option>
                            @foreach ($loanProducts as $loanProduct)
                                <option value="{{ $loanProduct->id }}"
                                    {{ $selectedLoanProductId == $loanProduct->id ? 'selected' : '' }}>
                                    {{ $loanProduct->Name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-dark mt-2">Filter</button>
                    </div>
                </form>

                <table border="1" class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th></th>
                            @foreach ($months as $month)
                                <th class="text-end">{{ \Carbon\Carbon::parse($month)->format('M Y') }}</th>
                            @endforeach
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Principal Projection</th>
                            @foreach ($months as $month)
                                <td class="text-end">{{ number_format($deferredIncomes[$month]['Principal Projection'], 2) }}</td>
                            @endforeach
                            <td class="text-end">{{ number_format(array_sum(array_column($deferredIncomes, 'Principal Projection')), 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Interest Projection</th>
                            @foreach ($months as $month)
                                <td class="text-end">{{ number_format($deferredIncomes[$month]['Interest Projection'], 2) }}</td>
                            @endforeach
                            <td class="text-end">{{ number_format(array_sum(array_column($deferredIncomes, 'Interest Projection')), 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Fees Projection</th>
                            @foreach ($months as $month)
                                <td class="text-end">{{ number_format($deferredIncomes[$month]['Fees Projection'], 2) }}</td>
                            @endforeach
                            <td class="text-end">{{ number_format(array_sum(array_column($deferredIncomes, 'Fees Projection')), 2) }}</td>
                        </tr>
                        <tr>
                            <th>Penalty Projection</th>
                            @foreach ($months as $month)
                                <td class="text-end">{{ number_format($deferredIncomes[$month]['Penalty Projection'], 2) }}</td>
                            @endforeach
                            <td class="text-end">{{ number_format(array_sum(array_column($deferredIncomes, 'Penalty Projection')), 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Total Projection</th>
                            @foreach ($months as $month)
                                <td class="text-end">{{ number_format($deferredIncomes[$month]['Total Projection'], 2) }}</td>
                            @endforeach
                            <td class="text-end">{{ number_format(array_sum(array_column($deferredIncomes, 'Total Projection')), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endSection
