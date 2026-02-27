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
                        <label for="Value" class="form-label" id="ValueLabel">Value <x-required /></label>
                        <input type="number" id="Value" class="form-control" step="0.01"
                            placeholder="Enter fee value" name="Value" value="{{ old('Value') }}">
                    </div>

                    <!-- Applicable On -->
                    <div class="mb-4">
                        <label for="Applicable_On" class="form-label">Applicable On <x-required /></label>
                        <select name="Applicable_On" id="Applicable_On" required class="form-select">
                            <option value="">Choose...</option>
                            <option value="None">None</option>
                            <option value="Principal">Principal</option>
                            <option value="Interest">Interest</option>
                            <option value="Balance">Total Loan Balance (Principal + Interest + ...)</option>
                            <option value="Balance">Installment Balance (Principal + Interest + ...)</option>
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
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="Description" class="form-label">Description <x-required /></label>
                        <textarea id="Description" class="form-control" placeholder="Enter fee description" name="Description" rows="3"
                            required>{{ old('Description') }}</textarea>
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
