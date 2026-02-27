@section('scripts')
    <script>
        function updateLabel() {
            const method = document.getElementById('Calculation_Method').value;
            const valueLabel = document.getElementById('ValueLabel');
            const valueField = document.getElementById("ValueField").closest('.mb-4');
            const tiersField = document.getElementById("TiersField");

            if (method === 'Flat') {
                valueLabel.textContent = 'Amount';
                valueField.style.display = "block";
                tiersField.style.display = "none";
            } else if (method === 'Percentage') {
                valueLabel.textContent = 'Rate';
                valueField.style.display = "block";
                tiersField.style.display = "none";
            } else if (method === 'Tiered') {
                valueLabel.textContent = 'Value';
                valueField.style.display = "none";
                tiersField.style.display = "block";
            }
            updateTiersJSON();
        }

        function updateTiersJSON() {
            const rows = document.querySelectorAll('#tier-rows .row');
            const tiers = [];

            rows.forEach(row => {
                const inputs = row.querySelectorAll('input');
                tiers.push({
                    min: parseFloat(inputs[0].value) || 0,
                    max: parseFloat(inputs[1].value) || 0,
                    value: parseFloat(inputs[2].value) || 0
                });
            });

            document.getElementById('tiers-json').value = JSON.stringify(tiers);
        }

        function removeTierRow(button) {
            button.closest('.row').remove();
            updateTiersJSON();
        }

        function addTierRow(min = '', max = '', value = '') {
            const row = document.createElement('div');
            row.classList.add('row', 'mb-2');
            row.innerHTML = `
        <div class="col-md-3">
            <input type="number" class="form-control" placeholder="Min" onchange="updateTiersJSON()" value="${min}">
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control" placeholder="Max" onchange="updateTiersJSON()" value="${max}">
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control" placeholder="Fee Value" onchange="updateTiersJSON()" value="${value}">
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-danger" onclick="removeTierRow(this)">Remove</button>
        </div>
    `;
            document.getElementById('tier-rows').appendChild(row);
            updateTiersJSON();
        }

        document.addEventListener("DOMContentLoaded", () => {
            updateLabel();

            // Determine if we're on edit mode or failed validation
            @php
                $tiersJson = old('Tiers') ?? ($fee->Tiers ?? '[]');
            @endphp

            @if (isset($tiersJson) && is_string($tiersJson))
                try {
                    const existingTiers = {!! json_encode(json_decode($tiersJson, true)) !!};
                    existingTiers.forEach(t => addTierRow(t.min, t.max, t.value));
                } catch (e) {
                    console.error("Invalid tiers JSON", e);
                }
            @endif
        });
    </script>
@endsection
