<div class="modal fade" id="editPenaltyModal{{ $penalty->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('loan-product-penalty.update', $penalty->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" value="{{ $loanProduct->id }}" name="Loan_Product_ID">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Edit Penalty - {{ $penalty->Name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Penalty Name -->
                    <div class="mb-4">
                        <label for="Name" class="form-label">Penalty Name <x-required /></label>
                        <input type="text" id="Name" class="form-control" placeholder="Enter penalty name"
                            name="Name" value="{{ old('Name', $penalty->Name) }}">
                    </div>

                    <!-- Calculation Method and Value -->
                    <div class="row g-6 mb-4">
                        <div class="col mb-0">
                            <label for="Calculation_Method" class="form-label">Calculation Method <x-required /></label>
                            <select id="Calculation_Method" class="form-control" name="Calculation_Method"
                                onchange="updateLabel()">
                                <option value="">Choose...</option>
                                <option value="Flat"
                                    {{ old('Calculation_Method', $penalty->Calculation_Method) == 'Flat' ? 'selected' : '' }}>
                                    Flat
                                </option>
                                <option value="Percentage"
                                    {{ old('Calculation_Method', $penalty->Calculation_Method) == 'Percentage' ? 'selected' : '' }}>
                                    Percentage
                                </option>
                            </select>
                        </div>
                        <div class="col mb-0">
                            <label for="Value" id="ValueLabel" class="form-label">Value <x-required /></label>
                            <input type="number" id="Value" class="form-control" step="0.01"
                                placeholder="Enter value" name="Value" value="{{ old('Value', $penalty->Value) }}">
                        </div>
                    </div>

                    <!-- Applicable On -->
                    <div class="mb-4">
                        <label for="Applicable_On" class="form-label">Applicable On <x-required /></label>
                        <select name="Applicable_On" id="Applicable_On" required class="form-select">
                            <option value="">Choose...</option>
                            <option value="Overdue Principal"
                                {{ $penalty->Applicable_On == 'Overdue Principal' ? 'selected' : '' }}>Overdue
                                Principal
                            </option>
                            <option value="Overdue Interest"
                                {{ $penalty->Applicable_On == 'Overdue Interest' ? 'selected' : '' }}>Overdue Interest
                            </option>
                            <option value="Overdue Principal And Interest"
                                {{ $penalty->Applicable_On == 'Overdue Principal And Interest' ? 'selected' : '' }}>
                                Overdue Principal And Interest</option>
                            <option value="Total Outstanding Balance"
                                {{ $penalty->Applicable_On == 'Total Outstanding Balance' ? 'selected' : '' }}>
                                Total Outstanding Balance</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="Description" class="form-label">Description <x-optional /></label>
                        <textarea id="Description" class="form-control" placeholder="Enter description" name="Description">{{ old('Description', $penalty->Description) }}</textarea>
                    </div>
                    @php
                        $fieldPrefix = "penalty_{$penalty->id}";
                    @endphp
                    <div class="mb-4">
                        <label for="{{ $fieldPrefix }}_Has_Recurring_Penalty" class="form-label">
                            Has Recurring Penalty <x-optional />
                        </label>
                        <select name="Has_Recurring_Penalty" id="{{ $fieldPrefix }}_Has_Recurring_Penalty"
                            class="form-select">
                            <option value="0"
                                {{ old('Has_Recurring_Penalty', $penalty->Has_Recurring_Penalty) == 0 ? 'selected' : '' }}>
                                No</option>
                            <option value="1"
                                {{ old('Has_Recurring_Penalty', $penalty->Has_Recurring_Penalty) == 1 ? 'selected' : '' }}>
                                Yes</option>
                        </select>
                    </div>

                    <div class="recurring-penalty-fields" id="{{ $fieldPrefix }}_fields">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="Recurring_Penalty_Interest_Value" class="form-label">
                                    Penalty Value <x-optional />
                                </label>
                                <input type="number" class="form-control" step="0.01"
                                    id="Recurring_Penalty_Interest_Value" name="Recurring_Penalty_Interest_Value"
                                    value="{{ old('Recurring_Penalty_Interest_Value', $penalty->Recurring_Penalty_Interest_Value) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="Recurring_Penalty_Interest_Period_Type" class="form-label">
                                    Period Type <x-optional />
                                </label>
                                <select name="Recurring_Penalty_Interest_Period_Type"
                                    id="Recurring_Penalty_Interest_Period_Type" class="form-select">
                                    <option value="">Choose...</option>
                                    <option value="Daily"
                                        {{ $penalty->Recurring_Penalty_Interest_Period_Type == 'Daily' ? 'selected' : '' }}>
                                        Daily</option>
                                    <option value="Weekly"
                                        {{ $penalty->Recurring_Penalty_Interest_Period_Type == 'Weekly' ? 'selected' : '' }}>
                                        Weekly</option>
                                    <option value="Monthly"
                                        {{ $penalty->Recurring_Penalty_Interest_Period_Type == 'Monthly' ? 'selected' : '' }}>
                                        Monthly</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="Penalty_Starts_After_Days" class="form-label">
                                    Penalty Starts After Days <x-optional />
                                </label>
                                <input type="number" class="form-control" id="Penalty_Starts_After_Days"
                                    name="Penalty_Starts_After_Days"
                                    value="{{ old('Penalty_Starts_After_Days', $penalty->Penalty_Starts_After_Days) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="Recurring_Penalty_Interest_Period_Value" class="form-label">
                                    Penalty Stops After Period <x-optional />
                                </label>
                                <input type="number" class="form-control"
                                    id="Recurring_Penalty_Interest_Period_Value"
                                    name="Recurring_Penalty_Interest_Period_Value"
                                    value="{{ old('Recurring_Penalty_Interest_Period_Value', $penalty->Recurring_Penalty_Interest_Period_Value) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    &nbsp;&nbsp;
                    <button type="submit" class="btn btn-dark">Update Penalty</button>
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
            valueLabel.textContent = 'Value';
        }
    }

    // Ensure correct label on page load if value is pre-selected
    document.addEventListener('DOMContentLoaded', function() {
        updateLabel();

        const dropdown = document.getElementById('{{ $fieldPrefix }}_Has_Recurring_Penalty');
        const fieldContainer = document.getElementById('{{ $fieldPrefix }}_fields');

        function toggleFields() {
            fieldContainer.style.display = dropdown.value === '1' ? 'block' : 'none';
        }

        dropdown.addEventListener('change', toggleFields);
        toggleFields(); // run initially on load
    });
</script>
