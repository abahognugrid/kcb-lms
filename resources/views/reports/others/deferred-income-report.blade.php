<!-- resources/views/reports/others/deferred-income-report.blade.php -->

@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Other Reports - Deferred Income Report')

@section('content')
    <div class="table-wrapper">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('reports.others.deferred-income-report') }}" method="GET">
                    <div class="form-group row">
                        <div class="col">
                            <label for="loan_product_id">Select Loan Product:</label>
                            <select id="loan_product_id" name="loan_product_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($loanProducts as $loanProduct)
                                    <option value="{{ $loanProduct->id }}"
                                        {{ $selectedLoanProductId == $loanProduct->id ? 'selected' : '' }}>
                                        {{ $loanProduct->Name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    value="{{ $startDate ?? '' }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                    value="{{ $endDate ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark mt-2">Filter</button>
                </form>

                <table border="1" class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th class="text-end">Deferred Principal</th>
                            <th class="text-end">Deferred Interest</th>
                            <th class="text-end">Deferred Fees</th>
                            <th class="text-end">Deferred Penalty</th>
                            <th class="text-end">Total Deferred Income</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deferredIncomes as $deferredIncome)
                            <tr>
                                <td>{{ $deferredIncome['loan_id'] }}</td>
                                <td class="text-end">{{ number_format($deferredIncome['Deferred_Principal'], 2) }}</td>
                                <td class="text-end">{{ number_format($deferredIncome['Deferred_Interest'], 2) }}</td>
                                <td class="text-end">{{ number_format($deferredIncome['Deferred_Fees'], 2) }}</td>
                                <td class="text-end">{{ number_format($deferredIncome['Deferred_Penalty'], 2) }}</td>
                                <td class="text-end">{{ number_format($deferredIncome['Total_Deferred_Income'], 2) }}</td>
                                <td class="text-end">{{ number_format($deferredIncome['Total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endSection
