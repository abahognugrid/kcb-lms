<div class="d-flex justify-content-center gap-2">
    <div class="d-flex align-items-center">
        <label for="end_date" class="me-1 form-label text-secondary fs-6">Start:</label>
        <input type="date" id="end_date" wire:model.live="startDate" max="{{ now()->toDateString() }}" class="form-control me-2 px-2 py-1">
    </div>
    <div class="d-flex align-items-center">
        <label for="end_date" class="me-1 form-label text-secondary fs-6">End:</label>
        <input type="date" id="end_date" wire:model.live="endDate" max="{{ now()->toDateString() }}" class="form-control me-2 px-2 py-1">
    </div>
</div>
