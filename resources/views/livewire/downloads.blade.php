<div class="">

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="mb-6 bg-white p-4 rounded-2 shadow">
        <div class="d-flex align-items-center flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input
                    type="text"
                    id="search"
                    wire:model.live="search"
                    placeholder="Search by filename"
                    class="form-control"
                >
            </div>

            <div class="d-flex align-items-center gap-2">
                <label for="reportTypeFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter</label>
                <select
                    id="reportTypeFilter"
                    wire:model.live="reportTypeFilter"
                    class="form-select"
                >
                    <option value="">All report types</option>
                    @foreach($reportTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button
                    wire:click="clearFilters"
                    class="btn btn-sm btn-outline-dark"
                >
                    Clear Filters
                </button>
            </div>
            <div>Downloaded files are stored for only 1 week. Please download your reports immediately.</div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-2 shadow p-4">
        @if($notifications->count() > 0)
            <div class="table-responsive overflow-auto">
                <table class="table table-striped">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="">
                                Report Type
                            </th>
                            <th class="text-uppercase">
                                Filename
                            </th>
                            <th class="">
                                Format
                            </th>
                            <th class="">
                                Generated
                            </th>
                            <th class="text-end">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($notifications as $notification)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $notification->data['report_type'] ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $notification->data['filename'] ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ ($notification->data['export_type'] ?? '') === 'pdf' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ strtoupper($notification->data['export_type'] ?? 'unknown') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="d-flex justify-content-end align-items-center gap-4">
                                        <a
                                            href="{{ route('downloads.download', $notification->id) }}"
                                            class="btn btn-sm"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-down-to-line-icon lucide-arrow-down-to-line"><path d="M12 17V3"/><path d="m6 11 6 6 6-6"/><path d="M19 21H5"/></svg>
                                        </a>
                                        <button
                                            wire:click="deleteReport('{{ $notification->id }}')"
                                            class="btn btn-link text-danger"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-icon lucide-trash"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No reports found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search || $reportTypeFilter)
                        No reports match your current filters.
                    @else
                        You have not generated any reports yet.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
