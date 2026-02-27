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
        <div class="col-lg-12">
            <div style="height: 25rem">
                <livewire:livewire-line-chart :line-chart-model="$lineChartModel" key="{{ $lineChartModel->reactiveKey() }}"/>
            </div>
        </div>
    </div>

</div>
