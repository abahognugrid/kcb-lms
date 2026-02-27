@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loans - No Repayments')
@section('content')
    <div class="table-wrapper">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">No Repayments</h5>
                <form action="{{ route('no-repayments') }}" method="GET">
                    <div class="d-flex align-items-center">
                        <label for="start_date" class="me-2">From Date:</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                            class="form-control me-2">
                        <label for="end_date" class="me-2">To Date:</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                            class="form-control me-2">
                        <label for="status" class="me-2">Status:</label>
                        <select id="status" name="status" class="form-select me-2">
                            <option value="">All</option>
                            @foreach ($statuses as $key => $value)
                                <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-dark">Filter</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-end">Loan#</th>
                            <th>Loan Start Date</th>
                            <th class="text-end">Principal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>{{ $loan->FirstName }} {{ $loan->LastName }}</td>
                                <td class="text-end">{{ $loan->LoanID }}</td>
                                <td>{{ $loan->LoanStartDate }}</td>
                                <td class="text-end"><x-money :value="$loan->Principal" /></td>
                                <td>{{ convertAccountStatusCodeToText($loan->Credit_Account_Status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
    @include('reports.partials.sticky-table-styling')
@endsection
