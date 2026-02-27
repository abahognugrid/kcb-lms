<div>
    <div class="d-flex justify-content-end mb-2">
        <x-end-date/>
    </div>

    <h6 style="text-align: center; font-weight:bold">Customer Registration Gender Distribution</h6>

    <livewire:livewire-pie-chart :pie-chart-model="$chart_model" key="{{ $chart_model->reactiveKey() }}" />
</div>
