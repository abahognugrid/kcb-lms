<div>
    <div class="d-flex justify-content-end align-items-center mb-2">
        <x-date-filter/>
        <div>
            <div class="dropdown">
                <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span>{{ str($selectedPeriod)->title() }}</span>
                    <i class="bx bx-dots-vertical-rounded bx-lg"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics" style="">
                    <button wire:click="setPeriod('daily')" class="dropdown-item" {{ $selectedPeriod === 'daily' ? 'disabled' : '' }}>Daily</button>
                    <button wire:click="setPeriod('weekly')" class="dropdown-item" {{ $selectedPeriod === 'weekly' ? 'disabled' : '' }}>Weekly</button>
                    <button wire:click="setPeriod('monthly')" class="dropdown-item" {{ $selectedPeriod === 'monthly' ? 'disabled' : '' }}>Monthly</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-9">
            <div style="height: 25rem">
                <livewire:livewire-line-chart :line-chart-model="$lineChartModel" key="{{ $lineChartModel->reactiveKey() }}"/>
            </div>
        </div>
        <div class="col-lg-3 p-6">
            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <span class="">Principal Paid</span>
                    <span class="fw-bold">{{ number_format($summary->get('principal_paid')) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <span class="">Interest Paid</span>
                    <span class="fw-bold">{{ number_format($summary->get('interest_paid')) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <span class="">Fees & Penalties Paid</span>
                    <span class="fw-bold">{{ number_format($summary->get('fees_paid') + $summary->get('penalties_paid')) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <span class="fw-semibold">Total Paid</span>
                    <span class="fw-bold">{{ number_format($summary->get('total_paid')) }}</span>
                </div>
{{--                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">--}}
{{--                    <span class="fw-semibold">Repayment Rate</span>--}}
{{--                    <span class="fw-bold">{{ 0 }}</span>--}}
{{--                </div>--}}
            </div>
        </div>"
    </div>

</div>
