<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Statement of Comprehensive Income</h4>
        <div class="d-flex justify-content-end align-items-center">
            <x-date-filter/>
            <x-export-buttons/>
        </div>
    </div>
    <div class="card-body">
        <h5 class="mt-4">Income</h5>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Account</th>
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
                    <td class="text-end"><x-money :value="$revenue->balance" /></td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>Total Income</th>
                <th class="text-end"><x-money :value="$total_revenue" /></th>
            </tr>
            </tfoot>
        </table>

        <h5 class="mt-4">Expenses</h5>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><strong>Account</strong></th>
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
                    <td class="text-end"><x-money :value="$expense->balance" /></td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>Total Expenses</th>
                <th class="text-end"><x-money :value="$total_expenses" /></th>
            </tr>
            </tfoot>
        </table>

        <h5 class="mt-4">Net Income</h5>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Net Income</th>
                <th class="text-end"><x-money :value="$net_income" /></th>
            </tr>
            </thead>
        </table>
    </div>
</div>
