<div class="mb-4">
    <div class="mb-4">
        <label for="Repayment_Order" class="form-label">Repayment Order <x-required /></label>
        <br><small class=" mb-4">Drag and Drop up and down to set your repayment priority.</small><br><br>
        <ul class="list-group" id="repaymentOrderList">
            @foreach ($Repayment_Order as $item)
                <li class="list-group-item" draggable="true" data-value="{{ $item }}">
                    <div class="d-flex justify-content-between">
                        <div>{{ $item }}</div>
                        <div><i class="bx bx-menu"></i></div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    <input type="hidden" name="repayment_order" id="repayment_order" value="{{ json_encode($Repayment_Order) }}">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('repaymentOrderList');

        let draggedItem = null;

        list.addEventListener('dragstart', function(e) {
            draggedItem = e.target;
            e.target.classList.add('dragging');
        });

        list.addEventListener('dragend', function(e) {
            e.target.classList.remove('dragging');
            draggedItem = null;
        });

        list.addEventListener('dragover', function(e) {
            e.preventDefault();
            const draggingElements = list.querySelectorAll('.list-group-item');
            const afterElement = getDragAfterElement(list, e.clientY);
            if (afterElement == null) {
                list.appendChild(draggedItem);
            } else {
                list.insertBefore(draggedItem, afterElement);
            }
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.list-group-item:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return {
                        offset: offset,
                        element: child
                    };
                } else {
                    return closest;
                }
            }, {
                offset: Number.NEGATIVE_INFINITY
            }).element;
        }

        list.addEventListener('drop', function() {
            const newOrder = [...list.children].map(item => item.dataset.value);
            @this.set('Repayment_Order', newOrder); // Update the Livewire property
        });
    });
</script>
