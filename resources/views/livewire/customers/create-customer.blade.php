<div>
    <form wire:submit="store">
        <div class="mb-4">
            <label for="First_Name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="First_Name" name="First_Name" required wire:model='First_Name' />
            @error('First_Name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="Last_Name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="Last_Name" name="Last_Name" required wire:model='Last_Name' />
            @error('Last_Name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="Other_Name" class="form-label">Other Name</label>
            <input type="text" class="form-control" id="Other_Name" name="Other_Name" wire:model='Other_Name' />
            @error('Other_Name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="Gender" class="form-label">Gender</label>
            <select class="form-select" id="type" name="Gender" required wire:model.change='Gender'>
                <option selected>Choose...</option>
                <option value="Male">Male </option>
                <option value="Female">Female</option>
            </select>
            @error('Gender')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="Marital_Status" class="form-label">Marital Status</label>
            <select class="form-select" id="type" name="Marital_Status" required wire:model.change='Marital_Status'>
                <option selected>Choose...</option>
                <option value="Single">Single </option>
                <option value="Married">Married</option>
                <option value="Divorced">Divorced</option>
                <option value="Widowed">Widowed</option>
                <option value="Separated">Separated</option>
            </select>
            @error('Marital_Status')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="Email_Address" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="Email_Address" name="Email_Address" required wire:model='Email_Address' />
            @error('Email_Address')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="Telephone_Number" class="form-label">Telephone Number</label>
            <input type="text" class="form-control" id="Telephone_Number" name="Telephone_Number" required wire:model='Telephone_Number' />
            @error('Telephone_Number')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="ID_Type" class="form-label">ID Type</label>
            <select class="form-select" id="type" name="ID_Type" required wire:model.change='ID_Type'>
                <option selected>Choose...</option>
                <option value="Country_ID">Country ID </option>
                <option value="Passport_Number">Passport Number</option>
                <option value="Refugee_Number">Refugee Number</option>
            </select>
            @error('ID_Type')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="ID_Number" class="form-label">ID Number</label>
            <input type="text" class="form-control" id="ID_Number" name="ID_Number" required wire:model='ID_Number' />
            @error('ID_Number')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="Date_of_Birth" class="form-label">Date Of Birth</label>
            <input type="date" class="form-control" id="Date_of_Birth" name="Date_of_Birth" required wire:model='Date_of_Birth' />
            @error('Date_of_Birth')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <button type="submit" class="btn btn-dark">Create Customer</button>
        </div>
    </form>
</div>
