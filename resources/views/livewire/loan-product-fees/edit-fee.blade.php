<div>
    <form wire:submit.prevent="store">
        <div class="mb-4">
            <label for="partner_id" class="form-label">Partner Name</label>
            <select class="form-select" id="type" name="partner_id" required>
                <option selected>Choose...</option>
                <?php foreach ($partners as $partner): ?>
                <option value="{{ $partner->id }}"
                    {{ old('partner_id', $partner->id) == $partner->id ? 'selected' : '' }}>
                    {{ $partner->Institution_Name }}</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="Loan_Product_ID" class="form-label">Product Name</label>
            <select class="form-select" id="type" name="Loan_Product_ID" required
                wire:model.change='Loan_Product_ID'>
                <option value="">Choose...</option>
                <?php foreach (App\Models\LoanProduct::all() as $product): ?>
                <option value="{{ $product->id }}"
                    {{ old('Loan_Product_ID', $product->id) == $product->id ? 'selected' : '' }}>{{ $product->Name }}
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="Name" class="form-label">Name</label>
            <input type="text" class="form-control" id="Name" name="Name" required wire:model='Name' />
        </div>
        <div class="mb-4">
            <label for="Calculation_Method" class="form-label">Calculation Method</label>
            <select class="form-select" id="type" name="Calculation_Method" required
                wire:model.change='Calculation_Method'>
                <option selected>Choose...</option>
                <option value="Percentage">Percentage</option>
                <option value="Amount">Amount</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="Value" class="form-label">Value</label>
            <input type="text" class="form-control" id="Value" name="Value" required wire:model='Value' />
        </div>
        <div class="mb-4">
            <label for="Applicable_On" class="form-label">Applicable On</label>
            <select class="form-select" id="type" name="Applicable_On" required wire:model.change='Applicable_On'>
                <option selected>Choose...</option>
                <option value="Balance">Balance</option>
                <option value="Principal">Principal</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="Applicable_At" class="form-label">Applicable At</label>
            <select class="form-select" id="type" name="Applicable_At" required wire:model.change='Applicable_At'>
                <option selected>Choose...</option>
                <option value="Disbursement">Disbursement</option>
                <option value="Repayment">Repayment</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="Description" class="form-label">Description</label>
            <textarea name="Description" id="Description" cols="30" rows="3" class="form-control" required
                wire:model='Description'></textarea>
            <div id="Description" class="form-text">Please describe in detail what
                this Fee is and what it's for in a sentence.</div>
        </div>
        <div>
            <button type="submit" class="btn btn-info">Update Fee</button>
        </div>
    </form>
</div>
