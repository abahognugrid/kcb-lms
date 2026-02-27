@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Statement of Comprehensive Income</h4>
        <p style="margin-top: 0; font-size: 10px">From {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <h5 class="mt-4 mb-0">Income</h5>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <th class="text-start">Account</th>
            <th class="text-end">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($revenues as $revenue)
            <tr>
                <td>
                    <span class="badge bg-label-primary">{{ $revenue->account->identifier }}</span>
                    <span class="text-black"><strong>{{ $revenue->account->name }}</strong></span>
                </td>
                <td class="text-end"><x-money :value="$revenue->total_credit" /></td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start">Total Income</th>
                <th class="text-end"><x-money :value="$total_revenue" /></th>
            </tr>
        </tfoot>
    </table>

    <h5 class="mt-4 mb-0">Expenses</h5>
    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Account</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($expenses as $expense)
            <tr>
                <td>
                    <span class="badge bg-label-primary">{{ $expense->account->identifier }}</span>
                    <span class="text-black"><strong>{{ $expense->account->name }}</strong></span>
                </td>
                <td class="text-end"><x-money :value="$expense->total_debit" /></td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start">Total Expenses</th>
                <th class="text-end"><x-money :value="$total_expenses" /></th>
            </tr>
        </tfoot>
    </table>

    <h5 class="mt-4 mb-0">Net Income</h5>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <th class="text-start">Net Income</th>
            <th class="text-end"><x-money :value="$net_income" /></th>
        </tr>
        </thead>
    </table>
    <x-print-footer/>
@endsection
