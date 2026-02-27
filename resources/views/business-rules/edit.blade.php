@extends('layouts/contentNavbarLayout')
@section('title', 'Business Rule - Edit')
@section('content')
    <style>
        .hidden {
            display: none;
        }
    </style>
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Business Rule : {{ $businessRule->Name }}</h5>
                </div>
                <div class="d-flex align-items-start row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <form action="{{ route('business-rule.update', $businessRule) }}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label for="partner_id" class="form-label">Partner Name</label>
                                    <select class="form-select" id="type" name="partner_id" required
                                        wire:model.change='partner_id'>
                                        <option value="">Choose...</option>
                                        <?php foreach ($partners as $partner): ?>
                                        <option value="{{ $partner->id }}"
                                            {{ old('Option', $businessRule->partner_id) == $partner->id ? 'selected' : '' }}>
                                            {{ $partner->Institution_Name }}</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="Exclusion_Parameter_ID" class="form-label">Parameter</label>
                                    <select class="form-select" id="type" name="Exclusion_Parameter_ID" required>
                                        <option value="">Choose...</option>
                                        <?php foreach (App\Models\ExclusionParameter::all() as $parameter): ?>
                                        <option value="{{ $parameter->id }}"
                                            {{ old('Option', $businessRule->Exclusion_Parameter_ID) == $parameter->id ? 'selected' : '' }}>
                                            {{ $parameter->Name }}</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php foreach ($options as $option): ?>
                                <div class="form-check-inline my-4">
                                    <input class="form-check-input" type="radio" name="Option"
                                        value="{{ $option }}"
                                        {{ old('Option', $option) == $businessRule->Option ? 'checked' : '' }} required />
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        <?= $option ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                                <div class="mb-4 hidden" id="rangeFields">
                                    <label for="minField" class="form-label">Minimum</label>
                                    <input type="text" id="minField" class="form-control" name="Minimum"
                                        value="{{ $businessRule->Minimum }}" />
                                    <br>
                                    <label for="maxField" class="form-label">Maximum</label>
                                    <input type="text" id="maxField" class="form-control" name="Maximum"
                                        value="{{ $businessRule->Maximum }}" />
                                </div>

                                <div class="mb-4 hidden" id="singleField">
                                    <label for="Value" class="form-label">Value</label>
                                    <input type="text" class="form-control" name="Value"
                                        value="{{ $businessRule->Value }}" />
                                </div>
                                <br><button type="submit" class="btn btn-dark">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('scripts')
    <script>
        // Get the radio buttons and input field containers
        const radioButtons = document.querySelectorAll('input[name="Option"]');
        const singleField = document.getElementById('singleField');
        const rangeFields = document.getElementById('rangeFields');

        // Function to update the visibility of fields based on the selected value
        function updateFields() {
            const selectedRadio = document.querySelector('input[name="Option"]:checked');

            if (selectedRadio) { // Check if a radio button is selected
                const selectedValue = selectedRadio.value;
                console.log(selectedRadio);
                if (selectedValue === 'Range') {
                    singleField.classList.add('hidden');
                    rangeFields.classList.remove('hidden');
                } else {
                    singleField.classList.remove('hidden');
                    rangeFields.classList.add('hidden');
                }
            }
        }

        // Add event listeners to each radio button
        radioButtons.forEach(radio => {
            radio.addEventListener('change', updateFields);
        });

        // Initialize the visibility on page load
        updateFields();
    </script>
@endsection
@endsection
