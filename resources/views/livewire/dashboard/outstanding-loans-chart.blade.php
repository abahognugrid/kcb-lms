<div>
    <div class="d-flex justify-content-end mb-2">
        <x-end-date/>
    </div>
    <div class="row">
        <div class="col-lg-9">
            <div style="height: 25rem">
                <livewire:livewire-line-chart :line-chart-model="$lineChartModel" key="{{ $lineChartModel->reactiveKey() }}"/>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <span class="">Due in Period</span>
                    <span class="fw-bold">{{ number_format($summary->get('due_in_period')) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <span class="">Past Due</span>
                    <span class="fw-bold">{{ number_format($summary->get('past_due')) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <span class="">Not yet Due</span>
                    <span class="fw-bold">{{ number_format($summary->get('not_yet_due')) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <span class="fw-semibold">Total Outstanding</span>
                    <span class="fw-bold">{{ number_format($summary->get('total_outstanding')) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
