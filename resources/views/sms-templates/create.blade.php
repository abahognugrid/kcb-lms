@extends('layouts/contentNavbarLayout')
@section('title', 'SMS Templates - Create')
@section('content')
    <script>
        function myFunction(variable) {
            var checkBox = variable;
            var text = document.getElementById("Template");
            if (checkBox.checked == true) {
                text.value += checkBox.value;

            } else {
                text.value = text.value.replace(checkBox.value, '');
            }
        }
    </script>
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">SMS Template</h5>
                </div>
                <div class="d-flex align-items-start row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <div>
                                <form action="{{ route('sms-template.store') }}" method='post'>
                                    @csrf
                                    <div class="mb-4">
                                        <label for="partner_id" class="form-label">Partner Name</label>
                                        <select class="form-select" id="type" name="partner_id" required>
                                            <?php foreach ($partners as $partner): ?>
                                            <option value="{{ $partner->id }}"
                                                {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                                {{ $partner->Institution_Name }}</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="Loan_Product_ID" class="form-label">Product Name</label>
                                        <select class="form-select" id="type" name="Loan_Product_ID" required>
                                            <option value="">Choose...</option>
                                            <?php foreach (App\Models\LoanProduct::all() as $product): ?>
                                            <option value="{{ $product->id }}"
                                                {{ old('Loan_Product_ID') == $product->id ? 'selected' : '' }}>
                                                {{ $product->Name }}</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="Day" class="form-label">Day</label>
                                        <input type="number" class="form-control" id="Day" name="Day"
                                            value="{{ old('Day') }}" required />
                                    </div>
                                    <div class="mb-4 form-check">
                                        @foreach ($variables as $variable)
                                            <div id="Variables">
                                                <input type="checkbox" value=":{{ $variable }}" id="{{ $variable }}"
                                                    class="form-check-input" onclick="myFunction(this)">
                                                <label for="{{ $variable }}"
                                                    class="form-check-label"><?= ":$variable" ?></label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mb-4">
                                        <label for="Template" class="form-label">Template</label>
                                        <textarea name="Template" id="Template" cols="30" rows="3" class="form-control" required>{{ old('Template') }}</textarea>
                                        <div id="Template" class="form-text">Please set how your message will look like for
                                            this product.</div>
                                    </div>
                                    <button type="submit" class="btn btn-dark">Submit</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
