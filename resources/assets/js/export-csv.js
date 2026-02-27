function exportTableToCSV(tableId, filename) {
    // Get the table data
    const table = document.getElementById(tableId);
    if (!table) {
        console.error(`Table with ID "${tableId}" not found.`);
        return;
    }

    const rows = table.querySelectorAll('tr');
    const data = [];

    // Loop through rows and cells to build the data array
    rows.forEach(row => {
        const rowData = [];
        row.querySelectorAll('th, td').forEach(cell => {
            rowData.push(cell.innerText);
        });
        data.push(rowData);
    });

    // Send the data to the server
    fetch('/export-csv', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // Add CSRF token for Laravel
        },
        body: JSON.stringify({ data })
    })
    .then(response => response.blob())
    .then(blob => {
        // Create a link to download the file
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename || 'export.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    })
    .catch(error => console.error('Error exporting CSV:', error));
}

// Attach the export function to buttons with the "export-csv" class
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.export-csv').forEach(button => {
        button.addEventListener('click', function () {
            const tableId = button.getAttribute('data-table-id');
            const filename = button.getAttribute('data-filename') || 'export.csv';
            exportTableToCSV(tableId, filename);
        });
    });
});