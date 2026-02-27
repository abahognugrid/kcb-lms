<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-3">
                <h5 class="mb-0">Audit Trail Report</h5>
            </div>
            <div class="col-md-9 d-flex justify-content-end align-items-center">
                <!-- Search Input -->
                <div class="me-3">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search user, IP, URL..."
                           class="form-control form-control-sm"
                           style="width: 200px;">
                </div>

                <!-- Event Filter -->
                <div class="me-3">
                    <select class="form-select form-select-sm" wire:model.change="event" style="width: 150px;">
                        @foreach($eventOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <x-date-filter />
                <x-export-buttons />
            </div>
        </div>
    </div>

    <div class="card-body">
        <x-session-flash />

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Performed On</th>
                        <th>URL</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                        <th>Performed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->user?->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($record->event) }}</td>
                            <td>
                                <small>{{ class_basename($record->auditable_type) }}</small>
                            </td>
                            <td>
                                <small class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $record->url }}">
                                    {{ $record->url }}
                                </small>
                            </td>
                            <td>{{ $record->ip_address }}</td>
                            <td>
                                <small class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $record->user_agent }}">
                                    {{ $record->user_agent }}
                                </small>
                            </td>
                            <td>
                                <small>{{ $record->created_at->format('d-m-Y H:i:s') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No audit records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $records->links() }}
        </div>
    </div>
</div>
