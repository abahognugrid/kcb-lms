<div>
    <div class="mb-4">
        <div class="d-flex flex-row justify-content-between mb-3">
            <p class="my-2">
                Add all the necessary provisions then approve to update the Chart of Accounts. This approval can only be
                done once.
            </p>
            <?php if (!Auth::user()->is_admin && $provisions->first() && !empty($provisions->first()->approved_at)): ?>
            <button type="button" wire:click="addBatchProvision()" class="btn btn-primary">New Provision</button>
            <?php endif; ?>
        </div>

        <div class="">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Classification</th>
                        <th class="text-end" style="width: 200px">Min. Days</th>
                        <th class="text-end" style="width: 200px"><small class="text-light">Use 0 for
                                unlimited</small><br>Max. Days</th>
                        <th class="text-end">Arrears Amount</th>
                        <th class="text-end">Suspended Interest</th>
                        <th class="text-end">Provision %ge</th>
                        <th class="text-end">Provision Amount</th>

                        @if ($provisions->first() && empty($provisions->first()->approved_at))
                            <th class="text-end">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($provisions as $provision)
                        <tr>
                            <td>{{ $provision->ageing_category }}</td>
                            <td class="text-end">{{ $provision->minimum_days }}</td>
                            <td class="text-end">{{ $provision->maximum_days }}</td>
                            <td class="text-end">{{ number_format($provision->arrears_amount) }}</td>
                            <td class="text-end">{{ number_format($provision->suspended_interest) }}</td>
                            <td class="text-end">{{ round($provision->provision_rate, 2) }}</td>
                            <td class="text-end">{{ number_format($provision->provision_amount) }}</td>
                            @if (empty($provision->approved_at))
                                <td class="text-end">
                                    <div class="d-flex justify-content-end">
                                        <button wire:click="editProvision({{ $provision->id }})" type="button"
                                            class="btn btn-outline-secondary btn-xs rounded-1 d-flex align-items-center gap-1 px-2 py-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-pencil-icon lucide-pencil">
                                                <path
                                                    d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                                <path d="m15 5 4 4" />
                                            </svg>
                                            <span>Edit</span>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td colspan="2"></td>
                        <td class="fw-bold text-end">{{ number_format($provisions->sum('arrears_amount')) }}</td>
                        <td class="fw-bold text-end">{{ number_format($provisions->sum('suspended_interest')) }}</td>
                        <td></td>
                        <td class="fw-bold text-end">{{ number_format($provisions->sum('provision_amount')) }}</td>
                        @if ($provisions->first() && empty($provisions->first()->approved_at))
                            <td></td>
                        @endif
                    </tr>

                    <tr class="border-none">
                        <td class="border-none">
                            <div class="form-group">
                                <input type="text" class="form-control" wire:model="ageing_category">
                                @error('ageing_category')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </td>
                        <td class="border-none">
                            <div class="form-group">
                                <input type="number" class="form-control text-end" wire:model="minimum_days">
                                @error('minimum_days')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </td>
                        <td class="border-none">
                            <div class="form-group">
                                <input type="number" class="form-control text-end" wire:model="maximum_days">
                                @error('maximum_days')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </td>
                        <td class="border-none text-end">
                            <span class="text-secondary">{{ number_format($arrears_amount) }}</span>
                        </td>
                        <td class="border-none"></td>
                        <td class="border-none">
                            <div class="form-group">
                                <input type="number" class="form-control text-end" wire:model="provision_rate"
                                    min="0" max="100">
                                @error('provision_rate')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </td>
                        <td class="border-none text-end">
                            <span class="text-secondary">{{ number_format($provision_amount) }}</span>
                        </td>
                        <td class="border-none">
                            <div class="d-flex justify-content-end align-items-end">
                                @if ($editingProvision)
                                    <button type="button" class="btn btn-dark btn-sm"
                                        wire:click="updateProvision({{ $provisionId }})">Update</button>
                                @else
                                    <button type="button" class="btn btn-dark btn-sm" wire:click="addProvision"
                                        {{ $maximizedProvision ? 'disabled' : '' }}>Add</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @if ($maximizedProvision)

                        @if ($requiresApproval)
                            <tr>
                                <td colspan="6" class="text-end">
                                    <small>You cannot add more provisions when any exists with maximum days set to zero
                                        (unlimited)</small>
                                </td>
                            </tr>
                            @if (Auth::user()->can('update loan-loss-provisions'))
                                <tr>
                                    <td colspan="6">
                                        <div>
                                            <div class="h6">Approve Loan Loss Provisions</div>
                                            <div class="d-flex">
                                                @if (!$approvedRequested)
                                                    <button type="button" class="btn btn-dark btn-sm"
                                                        x-on:click="$wire.set('approvedRequested', true)">Approve</button>
                                                @else
                                                    <div class="bg-lighter p-3 rounded">

                                                        <p>Are you sure you want to approve the loan loss provisions
                                                            above?
                                                        </p>
                                                        <p>Click "Confirm" to continue or "Cancel" to abort.</p>
                                                        <div class="d-flex gap-3">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary btn-sm"
                                                                x-on:click="$wire.set('approvedRequested', false)">Cancel</button>
                                                            <button type="button" class="btn btn-dark btn-sm"
                                                                wire:click="approveProvision">Confirm</button>
                                                        </div>
                                                        <div class="d-flex mt-3 fst-italic">
                                                            <small>Being approved by: {{ auth()->user()->name }} on
                                                                {{ date('Y-m-d') }} </small>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @else
                            <tr>
                                <td colspan="6">
                                    <p>
                                        <span>Approved By:
                                            <strong>{{ $provisions->first()->approvedBy->name }}</strong> at:
                                            <strong>{{ $provisions->first()->approved_at }}</strong></span>
                                    </p>
                                </td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
