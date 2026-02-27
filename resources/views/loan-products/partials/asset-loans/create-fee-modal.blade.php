<div class="modal fade" id="createAssetFeeModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('loan-product-fee.store', ['fee_type' => \App\Enums\FeeType::AssetFee->value]) }}"
                method="POST">
                @csrf
                @method('POST')
                <input type="hidden" value="{{ $loanProduct->id }}" name="Loan_Product_ID">
                <input type="hidden" value="{{ $loanProduct->partner_id }}" name="partner_id">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Create Asset Loan Fee
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
                        <select id="Calculation_Method" class="form-control" name="Calculation_Method"
                            onchange="updateLabel()">
                            <option value="">Choose...</option>
                            <option value="Flat" {{ old('Calculation_Method') == 'Flat' ? 'selected' : '' }}>Flat
                            </option>
                            <option value="Percentage"
                                {{ old('Calculation_Method') == 'Percentage' ? 'selected' : '' }}>Percentage</option>
                        </select>
                    </div>

                    <!-- Value -->
                    <div class="mb-4">
                        <label for="Value" class="form-label" id="Value">Value <x-required /></label>
                        <input type="number" id="Value" class="form-control" step="0.01"
                            placeholder="Enter fee value" name="Value" value="{{ old('Value') }}" required>
                    </div>

                    <!-- Applicable On -->
                    <div class="mb-4">
                        <label for="Applicable_On_Value" class="form-label">Applicable On Value</label>
                        <input type="number" name="Applicable_On_Value" id="Applicable_On_Value" required
                            class="form-control" />
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="Description" class="form-label">Description</label>
                        <textarea id="Description" class="form-control" placeholder="Enter fee description" name="Description" rows="3"
                            required>{{ old('Description') }}</textarea>
                    </div>

                    <!-- Tenor -->
                    <div class="mb-4">
                        <label for="Tenor" class="form-label">Tenor</label>
                        <input type="number" name="Tenor" id="Tenor" class="form-control" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    &nbsp;&nbsp;
                    <button type="submit" class="btn btn-dark">Create Fee</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function updateLabel() {
        const calculationMethod = document.getElementById('Calculation_Method').value;
        const valueLabel = document.getElementById('ValueLabel');

        if (calculationMethod === 'Flat') {
            valueLabel.textContent = 'Amount';
        } else if (calculationMethod === 'Percentage') {
            valueLabel.textContent = 'Rate';
        } else {
            valueLabel.textContent = 'Value'; // Default label when nothing is selected
        }
    }

    // Run the updateLabel function on page load in case there's a pre-selected value
    document.addEventListener('DOMContentLoaded', function() {
        updateLabel();
    });
</script>
