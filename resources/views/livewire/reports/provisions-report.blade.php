<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">Loan Loss Provisions Report</h5>
                <p>Arrears amounts are set as at date of approval.</p>
            </div>
            <div class="col-md-8">
                <div class="d-flex justify-content-end align-items-center gap-4">
                    <x-end-date />
                    <select class="form-select w-50" wire:model.live="loanProductId">
                        <option>Select Loan Product</option>
                        @foreach ($loanProducts as $loanProductId => $loanProductName)
                            <option value="{{ $loanProductId }}">{{ $loanProductName }}</option>
                        @endforeach
                    </select>
                    <x-export-buttons />
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Classification</th>
                    <th class="">Days</th>
                    <th class="text-end">Arrears Amount</th>
                    <th class="text-end">Suspended Interest</th>
                    <th class="text-end">Provision %ge</th>
                    <th class="text-end">Provision Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->ageing_category }}</td>
                        <td>{{ $record->days }}</td>
                        <td class="text-end">{{ number_format($record->arrears_amount) }}</td>
                        <td class="text-end">{{ number_format($record->suspended_interest) }}</td>
                        <td class="text-end">{{ round($record->provision_rate) }}</td>
                        <td class="text-end">{{ number_format($record->provision_amount) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="fw-bold">Total</td>
                    <td></td>
                    <td class="fw-bold text-end">{{ number_format($records->sum('arrears_amount')) }}</td>
                    <td class="fw-bold text-end">{{ number_format($records->sum('suspended_interest')) }}</td>
                    <td></td>
                    <td class="fw-bold text-end">{{ number_format($records->sum('provision_amount')) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
