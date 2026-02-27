<div class="modal fade" id="createTermsModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('loan-product-term.store') }}" method="POST">
                @csrf
                @method('POST')
                <input type="hidden" value="{{ $loanProduct->id }}" name="Loan_Product_ID">
                <input type="hidden" value="{{ $loanProduct->partner->id }}" name="partner_id">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Create Term
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <!-- Repayment Cycles -->
                    <div class="mb-4">
                        <label for="Repayment_Cycles" class="form-label mb-4">Repayment Cycles
                            <x-required /></label>
                        <div class="mb-4 form-check">
                            @foreach (\App\Models\LoanSchedule::REPAYMENT_FREQUENCIES as $item)
                                <div id="Repayment_Cycles">
                                    <input type="checkbox" name="Repayment_Cycles[]" value="{{ $item }}"
                                        id="{{ $item }}" class="form-check-input">
                                    <label for="{{ $item }}"
                                        class="form-check-label">{{ $item }}</label><br>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Interest Rate and Calculation Method -->
                    <div class="row g-6 mb-4">
                        <div class="col mb-0">
                            <label for="Interest_Rate" class="form-label">Interest Rate
                                <x-required /></label>
                            <input type="number" id="Interest_Rate" class="form-control" placeholder="Interest Rate"
                                name="Interest_Rate" value="{{ old('Interest_Rate') }}" step="0.001" />
                        </div>
                        <div class="col mb-0">
                            <label for="Interest_Cycle" class="form-label">Interest Cycle
                                <x-required /></label>
                            <select id="Interest_Cycle" class="form-control" name="Interest_Cycle">
                                @foreach (\App\Models\LoanSchedule::INTEREST_CYCLES as $item)
                                    <option value="{{ $item }}"
                                        {{ old('Interest_Cycle') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>

                            @error('Interest_Cycle')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col mb-0">
                            <label for="Interest_Calculation_Method" class="form-label">Interest
                                Calculation Method <x-required /></label>
                            <select id="Interest_Calculation_Method" class="form-control"
                                name="Interest_Calculation_Method">
                                <option value="">Choose...</option>
                                @foreach (\App\Models\LoanSchedule::SUPPORT_INTEREST_METHODS as $item)
                                    <option value="{{ $item }}"
                                        {{ old('Interest_Calculation_Method') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Term (Maximum) -->
                    <div class="mb-4">
                        <label for="Term" class="form-label">Term (Days) <x-required /></label>
                        <input type="text" id="Term" class="form-control" placeholder="Maximum Term allowed"
                            name="Value" value="{{ old('Term') }}">
                    </div>


                    <!-- Has Advance Payment -->
                    <div class="mb-4">
                        <input type="checkbox" class="form-check-input" id="Has_Advance_Payment"
                            name="Has_Advance_Payment" value="1"
                            {{ old('Has_Advance_Payment') ? 'checked' : '' }}>
                        <label for="Has_Advance_Payment" class="form-check-label">Has Advance Payment
                            <x-optional /></label>
                    </div>

                    <!-- Advance Calculation Method -->
                    <div class="mb-4">
                        <label for="Advance_Calculation_Method" class="form-label">Advance Calculation
                            Method <x-optional /></label>
                        <select id="Advance_Calculation_Method" class="form-control" name="Advance_Calculation_Method">
                            <option value="">Choose...</option>
                            @foreach (['Percentage', 'Flat'] as $item)
                                <option value="{{ $item }}"
                                    {{ old('Advance_Calculation_Method') == $item ? 'selected' : '' }}>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Advance Value -->
                    <div class="mb-4">
                        <label for="Advance_Value" class="form-label">Advance Value
                            <x-optional /></label>
                        <input type="number" class="form-control" id="Advance_Value" name="Advance_Value"
                            value="{{ old('Advance_Value') }}">
                    </div>

                    <!-- Extend Loan After Maturity -->
                    <div class="mb-4">
                        <input type="checkbox" class="form-check-input" id="Extend_Loan_After_Maturity"
                            name="Extend_Loan_After_Maturity" value="1"
                            {{ old('Extend_Loan_After_Maturity') ? 'checked' : '' }}>
                        <label for="Extend_Loan_After_Maturity" class="form-check-label">Extend Loan
                            After Maturity <x-optional /></label>
                    </div>

                    <!-- Interest Type After Maturity -->
                    <div class="mb-4">
                        <label for="Interest_Type_After_Maturity" class="form-label">Interest Type
                            After Maturity <x-optional /></label>
                        <input type="text" class="form-control" id="Interest_Type_After_Maturity"
                            name="Interest_Type_After_Maturity" value="{{ old('Interest_Type_After_Maturity') }}">
                    </div>

                    <!-- Interest Value After Maturity -->
                    <div class="mb-4">
                        <label for="Interest_Value_After_Maturity" class="form-label">Interest Value
                            After Maturity <x-optional /></label>
                        <input type="number" class="form-control" step="0.01" id="Interest_Value_After_Maturity"
                            name="Interest_Value_After_Maturity" value="{{ old('Interest_Value_After_Maturity') }}">
                    </div>

                    <!-- Interest After Maturity Calculation Method -->
                    <div class="mb-4">
                        <label for="Interest_After_Maturity_Calculation_Method" class="form-label">Interest After
                            Maturity Calculation Method
                            <x-optional /></label>
                        <input type="text" class="form-control" id="Interest_After_Maturity_Calculation_Method"
                            name="Interest_After_Maturity_Calculation_Method"
                            value="{{ old('Interest_After_Maturity_Calculation_Method') }}">
                    </div>

                    <!-- Recurring Period After Maturity Type -->
                    <div class="mb-4">
                        <label for="Recurring_Period_After_Maturity_Type" class="form-label">Recurring
                            Period After Maturity Type <x-optional /></label>
                        <input type="text" class="form-control" id="Recurring_Period_After_Maturity_Type"
                            name="Recurring_Period_After_Maturity_Type"
                            value="{{ old('Recurring_Period_After_Maturity_Type') }}">
                    </div>

                    <!-- Recurring Period After Maturity Value -->
                    <div class="mb-4">
                        <label for="Recurring_Period_After_Maturity_Value" class="form-label">Recurring Period After
                            Maturity Value
                            <x-optional /></label>
                        <input type="number" class="form-control" id="Recurring_Period_After_Maturity_Value"
                            name="Recurring_Period_After_Maturity_Value"
                            value="{{ old('Recurring_Period_After_Maturity_Value') }}">
                    </div>

                    <!-- Include Fees After Maturity -->
                    <div class="mb-4">
                        <input type="checkbox" class="form-check-input" id="Include_Fees_After_Maturity"
                            name="Include_Fees_After_Maturity" value="1"
                            {{ old('Include_Fees_After_Maturity') ? 'checked' : '' }}>
                        <label for="Include_Fees_After_Maturity" class="form-check-label">Include Fees
                            After Maturity <x-optional /></label>
                    </div>

                    <!-- Recurring Period After Maturity Value -->
                    <div class="mb-4">
                        <label for="Write_Off_After_Days" class="form-label">Write Off After Days
                            <x-optional /></label>
                        <input type="number" class="form-control" id="Write_Off_After_Days"
                            name="Write_Off_After_Days" value="{{ old('Write_Off_After_Days') }}">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    &nbsp;&nbsp;
                    <button type="submit" class="btn btn-dark">Create Term</button>
                </div>
            </form>
        </div>
    </div>
</div>
