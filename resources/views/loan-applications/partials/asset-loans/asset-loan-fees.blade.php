<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Asset Loan Fees</h5>
    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#createAssetFeeModal"
       class="btn btn-sm btn-primary" role="button">Add FEES to this product</a>
  </div>
  <div class="card-body">
    @if ($loanProduct->assetFees->isEmpty())
      <p>No fees available for this loan product.</p>
    @else
      @foreach ($loanProduct->assetFees as $fee)
        <ul class="list-group mb-4">
          <li class="list-group-item d-flex justify-content-between">
            <strong>
              Name:
            </strong>
            <span>
              {{ $fee->Name }}
            </span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <strong>
              Calculation Method:
            </strong>
            <span>
                {{ $fee->Calculation_Method }}
            </span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <strong>
              Value:
            </strong>
            <span>
                {{ number_format($fee->Value, 2) }}
            </span>
          </li>
          @if($fee->Applicable_On_Value)
            <li class="list-group-item d-flex justify-content-between">
              <strong>Applicable On Value:</strong>
              <span>{{ $fee->Applicable_On_Value }}</span>
            </li>
          @endif
          <li class="list-group-item d-flex justify-content-between">
            <strong>
              Description:
            </strong>
            <span>
                {{ $fee->Description }}
            </span>
          </li>
          @if($fee->Tenor)
          <li class="list-group-item d-flex justify-content-between">
            <strong>Tenor:</strong>
            <span>{{ $fee->Tenor }}</span>
          </li>
          @endif
          <li class="list-group-item d-flex justify-content-between">
            <strong>Actions:</strong>
            <div class="btn-group">
              <a class="btn btn-sm btn-outline-secondary" href="javascript:void(0);"
                 data-bs-toggle="modal" data-bs-target="#confirmDeleteFeeModal{{ $fee->id }}">
                                    <span class="text-danger">
                                        {{-- <i class="bx bx-trash"></i> &nbsp; --}}
                                        Remove
                                    </span>
              </a>
              <a class="btn btn-sm btn-outline-secondary" href="javascript:void(0);"
                 data-bs-toggle="modal" data-bs-target="#editFeeModal{{ $fee->id }}">
                                    <span class="text-primary">
                                        {{-- <i class="bx bx-edit"></i> &nbsp; --}}
                                        Edit
                                    </span>
              </a>
            </div>
          </li>
        </ul>
        <!-- Delete Modal -->
        @include('loan-products.partials.delete-fee-modal')
        <!-- Edit modal -->
        @include('loan-products.partials.edit-fee-modal')
      @endforeach
    @endif
  </div>
  <!--  Create Modal -->
  @include('loan-products.partials.asset-loans.create-fee-modal')
</div>
