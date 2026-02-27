<div>
    <form wire:submit="store">
        <div class="mb-4">
            <label for="Institution_Name" class="form-label">Institution Name</label>
            <input type="text" class="form-control" id="Institution_Name" name="Institution_Name" required
                wire:model='Institution_Name' />
        </div>
        <div class="mb-4">
            <label for="Identification_Code" class="form-label">Identification Code</label>
            <input type="text" class="form-control" id="Identification_Code" name="Identification_Code" required
                wire:model='Identification_Code' />
        </div>
        <div class="mb-4">
            <label for="Institution_Type" class="form-label">Institution Type</label>
            <select class="form-select" id="type" name="Institution_Type" wire:model.change='Institution_Type'>
                <option selected>Choose...</option>
                <option value="CB">Commercial Bank </option>
                <option value="MNO">Mobile Network Operator </option>
                <option value="VSLA">Village Savings & loans Association </option>
                <option value="CI">Credit Institution </option>
                <option value="NDT">Microfinance Non-Deposit Taking Institution</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="Accounting_Type" class="form-label">Accounting Type</label>
            <select class="form-select" id="type" name="Accounting_Type" wire:model.change='Accounting_Type'>
                <option selected>Choose...</option>
                <option value="Accrual">Accrual </option>
                <option value="Normal">Normal </option>
            </select>
        </div>
        <div class="mb-4">
            <label for="Access_Type" class="form-label">Access Type</label>
            <select class="form-select" id="type" name="Access_Type" required wire:model.change='Access_Type'>
                <option selected>Choose...</option>
                <option value="Loans">Loans</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="Email_Address" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="Email_Address" name="Email_Address" required
                wire:model='Email_Address' />
        </div>
        <div class="mb-4">
            <label for="Telephone_Number" class="form-label">Telephone Number</label>
            <input type="text" class="form-control" id="Telephone_Number" name="Telephone_Number" required
                wire:model='Telephone_Number' />
        </div>
        <div class="mb-4">
            <label for="License_Number" class="form-label">License Number</label>
            <input type="number" class="form-control" id="License_Number" name="License_Number" required
                wire:model='License_Number' />
        </div>
        <div class="mb-4">
            <label for="License_Issuing_Date" class="form-label">License Issuing Date</label>
            <input type="date" class="form-control" id="License_Issuing_Date" name="License_Issuing_Date" required
                wire:model='License_Issuing_Date' />
        </div>

        <div>
            <button type="submit" class="btn btn-dark">Create Partner</button>
        </div>
    </form>
</div>
