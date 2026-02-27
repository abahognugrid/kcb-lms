<div class="modal fade" id="confirmDeletePenaltyModal{{ $penalty->id }}" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('loan-product-penalty.destroy', $penalty->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this PENALTY configuration? This acttion can not be
                    undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    &nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger">Confirm deletion</button>
                </div>
            </form>
        </div>
    </div>
</div>
