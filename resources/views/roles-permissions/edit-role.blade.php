@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Edit Role')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('roles-permissions.update-role', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Role Name -->
                <div class="mb-4">
                    <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                </div>

                <!-- Partner -->
                <div class="mb-4">
                    <label for="partner_id" class="form-label">Partner <span class="text-danger">*</span></label>
                    <select class="form-select" id="partner_id" name="partner_id" required>
                        @foreach ($partners as $partner)
                            <option value="{{ $partner->id }}" {{ $role->partner_id == $partner->id ? 'selected' : '' }}>
                                {{ $partner->Institution_Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Permissions Section -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Assign Permissions</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                onclick="toggleAll(true)">Check All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="toggleAll(false)">Uncheck All</button>
                        </div>
                    </div>

                    <!-- Search Box -->
                    <div class="mb-3">
                        <input type="text" class="form-control" id="permissionSearch"
                            placeholder="Search permissions...">
                    </div>

                    @php
                        $groupedPermissions = $permissions->groupBy(function ($permission) {
                            $parts = explode(' ', $permission->name);
                            return $parts[1] ?? 'Other'; // group by second word
                        });
                    @endphp

                    @foreach ($groupedPermissions as $group => $groupPermissions)
                        <div class="mb-2 border rounded shadow-sm">
                            <div class="bg-light px-3 py-2 d-flex justify-content-between align-items-center"
                                data-bs-toggle="collapse" data-bs-target="#group-{{ Str::slug($group) }}" role="button"
                                aria-expanded="true">
                                <strong class="text-capitalize">{{ str_replace('_', ' ', $group) }}</strong>
                                <i class="bx bx-chevron-down"></i>
                            </div>

                            <div class="collapse show p-3" id="group-{{ Str::slug($group) }}">
                                <div class="row row-cols-1 row-cols-md-2 g-2">
                                    @foreach ($groupPermissions as $permission)
                                        <div class="col permission-item">
                                            <div class="form-check">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                    class="form-check-input" id="perm-{{ $permission->id }}"
                                                    {{ $role->permissions->contains($permission) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($permissions->isEmpty())
                        <p class="text-muted fst-italic">No permissions available.</p>
                    @endif
                </div>

                <!-- Submit -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Role Permissions</button>
                    {{-- <a href="{{ route('roles-permissions.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a> --}}
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Check All / Uncheck All for visible checkboxes only
        function toggleAll(checked) {
            document.querySelectorAll('.permission-item').forEach(item => {
                if (item.offsetParent !== null) {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox) checkbox.checked = checked;
                }
            });
        }

        // Filter permission groups based on search
        document.getElementById('permissionSearch').addEventListener('input', function() {
            const search = this.value.toLowerCase();

            document.querySelectorAll('[id^="group-"]').forEach(group => {
                const permissionItems = group.querySelectorAll('.permission-item');
                let groupHasVisibleItems = false;

                permissionItems.forEach(item => {
                    const label = item.textContent.toLowerCase();
                    const matches = label.includes(search);
                    item.style.display = matches ? 'block' : 'none';
                    if (matches) groupHasVisibleItems = true;
                });

                const wrapper = group.closest('.mb-2');
                wrapper.style.display = groupHasVisibleItems ? 'block' : 'none';
            });
        });
    </script>
@endsection
