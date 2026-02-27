<?php
use App\Services\Account\AccountSeederService;
use App\Models\Accounts\Account;

$payableAccounts = Account::query()
    ->select(['name', 'id'])
    ->where('partner_id', $loanProduct->partner_id)
    ->where(function ($query) {
        $query->where('identifier', 'like', AccountSeederService::PAYABLES_IDENTIFIER . '.%')->orWhere('identifier', 'like', AccountSeederService::INCOME_FROM_FINES_IDENTIFIER . '.%');
    })
    ->get();
?>
<div id="edit-fee-form-{{ $fee->id }}" class="fee-form">
    <div class="modal fade" id="editFeeModal{{ $fee->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('loan-product-fee.update', $fee->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" value="{{ $loanProduct->id }}" name="Loan_Product_ID">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Edit Fee - {{ $fee->Name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Name of the Fee -->
                        <div class="mb-4">
                            <label for="Name" class="form-label">Fee Name <x-required /></label>
                            <input type="text" id="Name" class="form-control" placeholder="Enter fee name"
                                name="Name" value="{{ old('Name', $fee->Name) }}">
                        </div>

                        <!-- Calculation Method -->
                        <div class="mb-4">
                            <label for="Calculation_Method" class="form-label">Calculation Method <x-required /></label>
                            <select id="Calculation_Method_{{ $fee->id }}" class="form-control"
                                name="Calculation_Method">
                                <option value="">Choose...</option>
                                <option value="Flat"
                                    {{ old('Calculation_Method', $fee->Calculation_Method) == 'Flat' ? 'selected' : '' }}>
                                    Flat
                                </option>
                                <option value="Percentage"
                                    {{ old('Calculation_Method', $fee->Calculation_Method) == 'Percentage' ? 'selected' : '' }}>
                                    Percentage</option>
                                <option value="Tiered"
                                    {{ old('Calculation_Method', $fee->Calculation_Method) == 'Tiered' ? 'selected' : '' }}>
                                    Tiered</option>
                            </select>
                        </div>

                        <!-- Value wrapper -->
                        <div class="mb-4" id="ValueField_{{ $fee->id }}">
                            <label for="Value_{{ $fee->id }}" class="form-label"
                                id="ValueLabel_{{ $fee->id }}">Value <x-required /></label>
                            <input type="number" id="Value_{{ $fee->id }}" class="form-control" step="0.01"
                                placeholder="Enter fee value" name="Value" value="{{ old('Value', $fee->Value) }}">
                        </div>

                        <!-- Tiers -->
                        <div class="mb-4" id="TiersField_{{ $fee->id }}" style="display: none;">
                            <label class="form-label">Tiered Fee Ranges <x-required /></label>
                            <div id="tier-rows_{{ $fee->id }}">
                                <!-- Rows inserted by JS -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                onclick="addTierRow_{{ $fee->id }}()">Add Tier</button>
                        </div>

                        <!-- Hidden tiers field -->
                        <input type="hidden" name="Tiers" id="tiers-json_{{ $fee->id }}">
                        <!-- Applicable On -->
                        <div class="mb-4">
                            <label for="Applicable_On" class="form-label">Applicable On <x-required /></label>
                            <select name="Applicable_On" id="Applicable_On" required class="form-select">
                                <option value="">Choose...</option>
                                <option {{ old('Applicable_On', $fee->Applicable_On) == 'None' ? 'selected' : '' }}
                                    value="None">None</option>
                                <option
                                    {{ old('Applicable_On', $fee->Applicable_On) == 'Principal' ? 'selected' : '' }}
                                    value="Principal">Principal</option>
                                <option {{ old('Applicable_On', $fee->Applicable_On) == 'Interest' ? 'selected' : '' }}
                                    value="Interest">Interest</option>
                                <option
                                    {{ old('Applicable_On', $fee->Applicable_On) == 'Installment Balance' ? 'selected' : '' }}
                                    value="Installment Balance">Installment Balance </option>
                                <option {{ old('Applicable_On', $fee->Applicable_On) == 'Balance' ? 'selected' : '' }}
                                    value="Balance">Total Loan Balance (Principal + Interest + ...)</option>
                            </select>
                        </div>
                        <!-- Charge Interest -->
                        <div class="mb-4">
                            <label for="Charge_Interest" class="form-label">Charge Interest <x-required /></label>
                            <select name="Charge_Interest" id="Charge_Interest" required class="form-select">
                                <option {{ old('Charge_Interest', $fee->Charge_Interest) == 'No' ? 'selected' : '' }}
                                    value="No">No</option>
                                <option {{ old('Charge_Interest', $fee->Charge_Interest) == 'Yes' ? 'selected' : '' }}
                                    value="Yes">Yes</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_part_of_interest"
                                    name="is_part_of_interest"
                                    {{ $fee->is_part_of_interest === 1 ? 'checked' : '' }} />
                                <label class="form-check-label" for="is_part_of_interest">
                                    Is this fee a component of the calculated Interest?
                                </label>
                            </div>
                        </div>

                        <!-- Applicable At -->
                        <div class="mb-4">
                            <label for="Applicable_At" class="form-label">Applicable At <x-required /></label>
                            <select name="Applicable_At" id="Applicable_At" required class="form-select">
                                <option value="">Choose...</option>
                                <option
                                    {{ old('Applicable_At', $fee->Applicable_At) == 'Disbursement' ? 'selected' : '' }}
                                    value="Disbursement">Disbursement</option>
                                <option
                                    {{ old('Applicable_At', $fee->Applicable_At) == 'Repayment' ? 'selected' : '' }}
                                    value="Repayment">Repayment</option>
                                <option
                                    {{ old('Applicable_At', $fee->Applicable_At) == 'Application' ? 'selected' : '' }}
                                    value="Application">Application</option>
                                <option {{ old('Applicable_At', $fee->Applicable_At) == 'Maturity' ? 'selected' : '' }}
                                    value="Maturity">Maturity</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="Payable_Account_ID" class="form-label">Payable Account </label>
                            <select name="Payable_Account_ID" id="Payable_Account_ID" class="form-select">
                                <option value="">Choose Payable Account...</option>
                                @foreach ($payableAccounts as $account)
                                    <option value="{{ $account->id }}"
                                        {{ old('Payable_Account_ID', $fee->Payable_Account_ID ?? '') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="Description" class="form-label">Description <x-optional /></label>
                            <textarea id="Description" class="form-control" placeholder="Enter fee description" name="Description"
                                rows="3">{{ old('Description', $fee->Description) }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Close</button>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-dark">Update Fee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const feeId = {{ $fee->id }};
            const form = document.getElementById('edit-fee-form-{{ $fee->id }}');

            if (!form) return;

            const methodSelect = document.getElementById(`Calculation_Method_${feeId}`);
            const valueLabel = document.getElementById(`ValueLabel_${feeId}`);
            const valueFieldWrapper = document.getElementById(`ValueField_${feeId}`);
            const tiersField = document.getElementById(`TiersField_${feeId}`);
            const tiersJsonInput = document.getElementById(`tiers-json_${feeId}`);
            const tierRows = document.getElementById(`tier-rows_${feeId}`);

            window['updateTiersJSON_' + feeId] = function() {
                const rows = tierRows.querySelectorAll('.row');
                const tiers = [];

                rows.forEach(row => {
                    const inputs = row.querySelectorAll('input');
                    console.log('Updating tiers JSON for fee ID:', feeId, 'Inputs:', inputs);
                    tiers.push({
                        min: parseFloat(inputs[0].value) || 0,
                        max: parseFloat(inputs[1].value) || 0,
                        value: parseFloat(inputs[2].value) || 0,
                        payableAmount: parseFloat(inputs[3].value) || 0
                    });
                });

                if (tiersJsonInput) {
                    tiersJsonInput.value = JSON.stringify(tiers);
                }
            };


            // Scoped tier adder
            window[`addTierRow_${feeId}`] = function(min = '', max = '', value = '', payableAmount = '') {
                const row = document.createElement('div');
                row.classList.add('row', 'mb-2');
                row.innerHTML = `
            <div class="col-md-2">
                <input type="number" class="form-control" placeholder="Min" onchange="updateTiersJSON_{{ $fee->id }}()" value="${min}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" placeholder="Max" onchange="updateTiersJSON_{{ $fee->id }}()" value="${max}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" placeholder="Fee Value" onchange="updateTiersJSON_{{ $fee->id }}()" value="${value}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" placeholder="Payable Amount" onchange="updateTiersJSON_{{ $fee->id }}()" value="${payableAmount}">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.row').remove(); updateTiersJSON_{{ $fee->id }}()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            `;
                tierRows.appendChild(row);
                window['updateTiersJSON_' + feeId]?.();
            };

            // Scoped label updater
            window[`updateLabel_${feeId}`] = function() {
                const method = methodSelect.value;

                if (method === 'Flat') {
                    valueLabel.textContent = 'Amount';
                    valueFieldWrapper.style.display = "block";
                    tiersField.style.display = "none";
                } else if (method === 'Percentage') {
                    valueLabel.textContent = 'Rate';
                    valueFieldWrapper.style.display = "block";
                    tiersField.style.display = "none";
                } else if (method === 'Tiered') {
                    valueLabel.textContent = 'Value';
                    valueFieldWrapper.style.display = "none";
                    tiersField.style.display = "block";
                }

                window['updateTiersJSON_' + feeId]?.();
            };

            methodSelect.addEventListener('change', window[`updateLabel_${feeId}`]);
            window[`updateLabel_${feeId}`]();

            // Load existing tiered values
            @php
                $tiersJson = old('Tiers') ?? ($fee->Tiers ?? '[]');
            @endphp

            @if (isset($tiersJson) && is_string($tiersJson))
                const existingTiers = {!! json_encode(json_decode($tiersJson, true)) !!};
                existingTiers.forEach(t => window[`addTierRow_{{ $fee->id }}`](t.min, t.max, t.value, t
                    .payableAmount));
            @endif
        });
    </script>
@endsection
