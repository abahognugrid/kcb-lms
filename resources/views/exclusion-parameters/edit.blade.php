@extends('layouts/contentNavbarLayout')
@section('title', 'Exclusion Parameter - Create')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Exclusion Parameter : {{ $exclusionParameter->Name }}</h5>
                </div>
                <div class="d-flex align-items-start row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <div>
                                <form action="{{ route('exclusion-parameter.update', $exclusionParameter) }}" method='post'>
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-4">
                                        <label for="Name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="Name" name="Name"
                                            value="{{ $exclusionParameter->Name }}" required />
                                    </div>
                                    <div class="mb-4">
                                        <label for="Parameter" class="form-label">Parameter</label>
                                        <input type="text" class="form-control" id="Parameter" name="Parameter"
                                            value="{{ $exclusionParameter->Parameter }}" required />
                                    </div>
                                    <div class="mb-4">
                                        <label for="Model" class="form-label">Model</label>
                                        <input type="text" class="form-control" id="Model" name="Model"
                                            value="{{ $exclusionParameter->Model }}" required />
                                    </div>
                                    <div class="mb-4">
                                        <label for="Type" class="form-label">Type</label>
                                        <select class="form-select" id="type" name="Type" required>
                                            <option value="">Choose...</option>
                                            <option value="Range"
                                                {{ $exclusionParameter->Type == 'Range' ? 'selected' : '' }}>Range</option>
                                            <option value="Boolean"
                                                {{ $exclusionParameter->Type == 'Boolean' ? 'selected' : '' }}>Boolean
                                            </option>
                                            <option value="List"
                                                {{ $exclusionParameter->Type == 'List' ? 'selected' : '' }}>List</option>
                                            <option value="Value"
                                                {{ $exclusionParameter->Type == 'Value' ? 'selected' : '' }}>Value</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-success">Submit</button>
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
