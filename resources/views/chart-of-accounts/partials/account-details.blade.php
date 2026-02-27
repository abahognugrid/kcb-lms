<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-start">
            {{ $account->partner?->Institution_Name }}: {{ $account->identifier }}-{{ $account->name }}
        </h5>
        <span>
            <a class="btn btn-secondary btn-sm" href="{{ route('chart-of-accounts.index') }}">
                <i class="menu-icon tf-icons bx bx-arrow-back"></i>
                Back
            </a>
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="mt-4">
                <div class="list-group">
                    @foreach ($account->childFromSlug($account->slug, $account->id, $account->partner_id) as $level1)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="menu-icon tf-icons bx bx-arrow-from-left"></i>
                                {{ $level1->identifier }} - {{ $level1->name }}

                                {{-- Toggle link --}}
                                <a href="#" class="text-warning"
                                    onclick="toggleForm('childForm-{{ $level1->id }}')">
                                    (<i class="fa fa-add"></i> Add account)
                                </a>
                            </span>
                            <span>
                                <x-money :value="$level1->current_balance" />
                            </span>
                        </div>

                        {{-- Hidden form for creating a child account --}}
                        <div id="childForm-{{ $level1->id }}"
                            style="display: none; margin-left: 20px; margin-top: 10px;">
                            <form action="{{ route('chart-of-accounts.store') }}" method="POST">
                                @csrf
                                @method('POST')
                                <div class="card">
                                    <div class="card-body">
                                        {{-- Pass the parent ID so your controller knows who the parent is --}}
                                        <input type="hidden" name="parent_id" value="{{ $level1->id }}">

                                        {{-- Add any other fields your store method expects --}}
                                        <div class="mb-2">
                                            <label for="name-{{ $level1->id }}">Account Name <x-required /></label>
                                            <input type="text" name="name" id="name-{{ $level1->id }}"
                                                class="form-control mt-2" placeholder="New child account name" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="identifier-{{ $level1->id }}">Identifier
                                                <x-required /></label>
                                            <input type="text" name="identifier" id="identifier-{{ $level1->id }}"
                                                class="form-control mt-2" placeholder="e.g., 1001" required>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="toggleForm('childForm-{{ $level1->id }}')">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Create</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if ($level1)
                            @foreach ($account->childFromSlug($level1->slug, $level1->id, $account->partner_id) as $level2)
                                @if ($level2)
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center text-primary">
                                        <a href="#" class="text-primary"
                                            onclick="toggleFormEdit('editchildForm-{{ $level2->id }}')"><span
                                                style="margin-left: 35px;">
                                                <i class="menu-icon tf-icons bx bx-arrow-from-left"></i>
                                                {{ $level2->identifier }} - {{ $level2->name }}
                                            </span></a>
                                        <span>
                                            <x-money :value="$level2->current_balance" />
                                        </span>

                                        @foreach ($account->childFromSlug($level2->slug, $level2->id, $account->partner_id) as $level3)
                                            @if ($level3)
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span style="margin-left: 70px;">
                                                        <i class="menu-icon tf-icons bx bx-arrow-from-left"></i>
                                                        {{ $level3->identifier }} - {{ $level3->name }}
                                                    </span>
                                                    <span>
                                                        <x-money :value="$level2->current_balance" />
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    {{-- Hidden form for editing a child account --}}
                                    <div id="editchildForm-{{ $level2->id }}"
                                        style="display: none; margin-left: 20px; margin-top: 10px;">
                                        <form action="{{ route('chart-of-accounts.update', $level2->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('put')
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5>Edit Account Details</h5>
                                                    {{-- Pass the parent ID so your controller knows who the parent is --}}
                                                    <input type="hidden" name="parent_id" value="{{ $level2->id }}">

                                                    {{-- Add any other fields your store method expects --}}
                                                    <div class="mb-2">
                                                        <label for="name-{{ $level2->id }}">Account Name
                                                            <x-required /></label>
                                                        <input type="text" name="name"
                                                            id="name-{{ $level2->id }}" class="form-control mt-2"
                                                            value="{{ $level2->name }}" required>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="toggleFormEdit('editchildForm-{{ $level2->id }}')">Cancel</button>
                                                    <button type="submit"
                                                        class="btn btn-sm btn-primary">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleForm(formId) {
        const formElement = document.getElementById(formId);
        if (formElement.style.display === 'none') {
            formElement.style.display = 'block';
        } else {
            formElement.style.display = 'none';
        }
    }

    function toggleFormEdit(formId) {
        const formElement = document.getElementById(formId);
        if (formElement.style.display === 'none') {
            formElement.style.display = 'block';
        } else {
            formElement.style.display = 'none';
        }
    }
</script>
