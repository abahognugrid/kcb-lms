<?php
use App\Services\Account\AccountSeederService;
use App\Models\Accounts\Account;
$user = Auth::user();
$payableAccounts = Account::query()
    ->select(['name', 'id'])
    ->where('partner_id', $loanProduct->partner_id)
    ->where(function ($query) {
        $query
            ->where('identifier', 'like', AccountSeederService::PAYABLES_IDENTIFIER . '.%')
            ->orWhere('identifier', 'like', AccountSeederService::INCOME_FROM_FINES_IDENTIFIER . '.%')
            ->orWhere('slug', AccountSeederService::INTEREST_INCOME_FROM_LOANS_SLUG);
    })
    ->get();
?>
<div id="create-fee-form" class="fee-form">
    <div class="modal fade" id="createFeeModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('loan-product-fee.store') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" value="{{ $loanProduct->id }}" name="Loan_Product_ID">
                    <input type="hidden" value="{{ $loanProduct->partner->id }}" name="partner_id">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Create Fee
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Name of the Fee -->
                        <div class="mb-4">
                            <label for="Name" class="form-label">Fee Name <x-required /></label>
                            <input type="text" id="Name" class="form-control" placeholder="Enter fee name"
                                name="Name" value="{{ old('Name') }}">
                        </div>

                        <!-- Calculation Method -->

                        <div class="mb-4">
                            <label for="Calculation_Method" class="form-label">Calculation Method <x-required /></label>
                            <select id="Calculation_Method_create" class="form-control" name="Calculation_Method">
                                <option value="">Choose...</option>
                                <option value="Flat" {{ old('Calculation_Method') == 'Flat' ? 'selected' : '' }}>Flat
                                </option>
                                <option value="Percentage"
                                    {{ old('Calculation_Method') == 'Percentage' ? 'selected' : '' }}>Percentage
                                </option>
                                <option value="Tiered" {{ old('Calculation_Method') == 'Tiered' ? 'selected' : '' }}>
                                    Tiered
                                </option>
                            </select>
                        </div>

                        <!-- Value Field -->
                        <div class="mb-4" id="ValueField_create">
                            <label for="Value_create" class="form-label" id="ValueLabel_create">Value
                                <x-required /></label>
                            <input type="number" id="Value_create" class="form-control" step="0.01"
                                placeholder="Enter fee value" name="Value" value="{{ old('Value') }}">
                        </div>

                        <!-- Dynamic Tiered Rows -->
                        <div class="mb-4" id="TiersField_create" style="display: none;">
                            <label class="form-label">Tiered Fee Ranges <x-required /></label>
                            <div id="tier-rows_create">
                                <!-- Rows added by JS -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                onclick="addTierRow_create()">Add Tier</button>

                        </div>

                        <!-- Hidden field for JSON payload -->
                        <input type="hidden" name="Tiers" id="tiers-json_create">


                        <!-- Applicable On -->
                        <div class="mb-4">
                            <label for="Applicable_On" class="form-label">Applicable On <x-required /></label>
                            <select name="Applicable_On" id="Applicable_On" required class="form-select">
                                <option value="">Choose...</option>
                                <option value="None">None</option>
                                <option value="Principal">Principal</option>
                                <option value="Interest">Interest</option>
                                <option value="Installment Balance">Installment Balance
                                </option>
                                <option value="Balance">Total Loan Balance (Principal + Interest + ...)</option>
                            </select>
                        </div>

                        <!-- Applicable At -->
                        <div class="mb-4">
                            <label for="Applicable_At" class="form-label">Applicable At <x-required /></label>
                            <select name="Applicable_At" id="Applicable_At" required class="form-select">
                                <option value="">Choose...</option>
                                <option value="Disbursement">Disbursement</option>
                                <option value="Repayment">Repayment</option>
                                <option value="Application">Application</option>
                                <option value="Maturity">Maturity</option>
                            </select>
                        </div>
                        <!-- Applicable On -->
                        <div class="mb-4">
                            <label for="Charge_Interest" class="form-label">Charge Interest <x-required /></label>
                            <select name="Charge_Interest" id="Charge_Interest" required class="form-select">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1"
                                    id="is_part_of_interest" name="is_part_of_interest">
                                <label class="form-check-label" for="is_part_of_interest">
                                    Is this fee a component of the calculated Interest?
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="Payable_Account_ID" class="form-label">Payable Account</label>
                            <select name="Payable_Account_ID" id="Payable_Account_ID" class="form-select">
                                <option value="">Choose Payable Account...</option>
                                @foreach ($payableAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Description -->
                        <div class="mb-4">
                            <label for="Description" class="form-label">Description <x-required /></label>
                            <textarea id="Description" class="form-control" placeholder="Enter fee description" name="Description"
                                rows="3" required>{{ old('Description') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Close</button>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-dark">Create Fee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById('create-fee-form');
            if (!form) return;

            const methodSelect = document.getElementById('Calculation_Method_create');
            const valueLabel = document.getElementById('ValueLabel_create');
            const valueField = document.getElementById('ValueField_create');
            const valueFieldWrapper = valueField?.closest('.mb-4');
            const tiersField = document.getElementById('TiersField_create');
            const tiersJsonInput = document.getElementById('tiers-json_create');
            const tierRows = document.getElementById('tier-rows_create');

            window.updateTiersJSON_create = function() {
                const rows = document.querySelectorAll('#tier-rows_create .row');
                const tiers = [];

                rows.forEach(row => {
                    const inputs = row.querySelectorAll('input');
                    tiers.push({
                        min: parseFloat(inputs[0].value) || 0,
                        max: parseFloat(inputs[1].value) || 0,
                        value: parseFloat(inputs[2].value) || 0,
                        payableAmount: parseFloat(inputs[3].value) || 0
                    });
                });

                document.getElementById('tiers-json_create').value = JSON.stringify(tiers);
            };


            window.addTierRow_create = function(min = '', max = '', value = '', payableAmount = '') {
                console.log('Adding tier row for create fee modal');
                const row = document.createElement('div');
                row.classList.add('row', 'mb-2');
                row.innerHTML = `
            <div class="col-md-2">
                <input type="number" class="form-control" placeholder="Min" onchange="updateTiersJSON_create()" value="${min}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" placeholder="Max" onchange="updateTiersJSON_create()" value="${max}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" placeholder="Fee Value" onchange="updateTiersJSON_create()" value="${value}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" placeholder="Payable Amount" onchange="updateTiersJSON_create()" value="${payableAmount}">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.row').remove(); updateTiersJSON_create()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
                tierRows.appendChild(row);
                updateTiersJSON_create();
            };

            window.updateLabel_create = function() {
                console.log('Updating label for create fee modal');
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

                updateTiersJSON_create();
            }

            methodSelect.addEventListener('change', updateLabel_create);
            updateLabel_create();

            // Correct key is 'Tiers', not 'tiers'
            @if (old('Calculation_Method') === 'Tiered' && old('Tiers'))
                const existingTiers = {!! json_encode(json_decode(old('Tiers', '[]'), true)) !!};
                existingTiers.forEach(t => addTierRow_create(t.min, t.max, t.value, t.payableAmount));
            @endif
        });
    </script>
@endsection
