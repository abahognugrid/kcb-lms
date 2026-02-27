<div>
    <div class="row">
        <div class="col order-1 mb-6">
            <h6 style="text-align: center; font-weight:bold">Loans Disbursed Purpose Distribution </h6>
            <div class="row">
                @foreach ($disbursementCharts as $productName => $chart)
                    <div class="col-12 mb-4 mt-2 border-bottom pb-3">
                        <livewire:livewire-pie-chart :pie-chart-model="$chart" key="{{ $chart->reactiveKey() }}" />
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col order-1 mb-6">
            <h6 style="text-align: center; font-weight:bold">Loans Paid Back Purpose Distribution </h6>
            <div class="row">
                @foreach ($repaymentCharts as $productName => $chart)
                    <div class="col-12 mb-4 mt-2 border-bottom pb-3">
                        <livewire:livewire-pie-chart :pie-chart-model="$chart" key="{{ $chart->reactiveKey() }}" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
