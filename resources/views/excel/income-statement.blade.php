<table class="table table-bordered">
    <thead>
    <tr>
        <th colspan="2" style="text-align: center; font-weight: bold; font-size: 18px">{{ $partnerName }}</th>
    </tr>
    <tr>
        <th colspan="2" style="text-align: center; font-weight: bold; font-size: 14px">Statement of Comprehensive Income</th>
    </tr>
    <tr>
        <th colspan="2" style="text-align: center; font-weight: bold; font-size: 12px">From {{ data_get($filters, 'startDate') }} to {{ data_get($filters, 'endDate') }}</th>
    </tr>
    <tr>
        <th colspan="2"></th>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">Income</th>
    </tr>
    <tr class="table-header">
        <th class="text-start" style="font-weight: bold; width: 300px">Account</th>
        <th class="text-end" style="font-weight: bold; text-align: right; width: 100px;">Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($records->revenues as $revenue)
        <tr>
            <td>
                <span class="badge bg-label-primary">{{ $revenue->account->identifier }}</span>
                <span>{{ $revenue->account->name }}</span>
            </td>
            <td class="text-end" style="text-align: right;">{{ $revenue->total_credit }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text-start" style="font-weight: bold;">Total Income</th>
            <th class="text-end" style="text-align: right; font-weight: bold;">{{ $records->total_revenue }}</th>
        </tr>
    </tfoot>
</table>

<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="2" style="font-weight: bold;">Expenses</th>
        </tr>
        <tr class="table-header">
            <th class="text-start" style="font-weight: bold; ">Account</th>
            <th class="text-end" style="font-weight: bold; text-align: right;">Amount</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($records->expenses as $expense)
        <tr>
            <td>
                <span class="badge bg-label-primary">{{ $expense->account->identifier }}</span>
                <span class="text-black">{{ $expense->account->name }}</span>
            </td>
            <td class="text-end" style="text-align: right;">{{ $expense->total_debit }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text-start" style="font-weight: bold;">Total Expenses</th>
            <th class="text-end" style="text-align: right; font-weight: bold;">{{ $records->total_expenses }}</th>
        </tr>
    </tfoot>
</table>
<table class="table table-bordered">
    <thead>
        <tr class="table-header">
            <th class="text-start" style="font-weight: bold;">Net Income</th>
            <th class="text-end" style="text-align: right; font-weight: bold;">{{ $records->net_income }}</th>
        </tr>
    </thead>
</table>
<x-print-footer/>
