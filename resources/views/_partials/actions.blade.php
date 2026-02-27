<div>
    <div class="dropdown">
        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
                class="bx bx-dots-vertical-rounded"></i>
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route($route.'.edit', $rowId) }}">
                <i class="bx bx-edit-alt me-1"></i> Edit
            </a>
            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal"
                data-bs-target="#confirmDeleteModal{{ $rowId }}">
                <i class="bx bx-trash me-1"></i> Delete
            </a>
        </div>
    </div>

    <div>
        <!-- Modal -->
        <div class="modal fade" id="confirmDeleteModal{{ $rowId }}" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm Deletion
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this record? ({{ $resourceName }}) <br> This action can
                        not be undone.
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route($route.'.destroy', $rowId) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            &nbsp;&nbsp;
                            <button type="submit" class="btn btn-danger">Confirm
                                deletion</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
