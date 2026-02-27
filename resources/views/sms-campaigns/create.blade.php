@extends('layouts/contentNavbarLayout')
@section('title', 'Sms Campaign - Create')
@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Create SMS Campaign</h3>
            </div>
            <div class="card-body row">
                <div class="col-md-7">
                    <form action="{{ route('sms-campaigns.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="partner_id" class="form-label">Partner Name</label>
                            <select class="form-select" id="partner_id" name="partner_id" required
                                onchange="toggleCustomerList()">
                                <option selected>Choose...</option>
                                <?php foreach ($partners as $partner): ?>
                                <option value="{{ $partner->id }}">{{ $partner->Institution_Name }}</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-5">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="form-group mb-5">
                            <label for="message">Message:</label>
                            <textarea class="form-control" name="message" required></textarea>
                        </div>

                        <div class="form-group mb-5">
                            <label for="scheduled_at">Scheduled At:</label>
                            <input type="datetime-local" class="form-control" name="scheduled_at">
                        </div>

                        <div class="form-group mb-5">
                            <label for="target_group">Target Group:</label>
                            <select class="form-control" name="target_group" id="target_group" required
                                onchange="toggleCustomerList()">
                                <option value="">Please select</option>
                                <option value="Loan_Holders">Loan Holders</option>
                                <option value="Pending_Applications">Pending Applications</option>
                                <option value="Rejected_Applications">Rejected Applications</option>
                                <option value="Active_Saving_Accounts">Active Saving Accounts</option>
                                <option value="Inactive_Saving_Accounts">Inactive Saving Accounts</option>
                                <option value="Savers_Opted_In">Savers Not Opted In</option>
                                <option value="Savers_Not_Opted_In">Savers Not Opted In</option>
                            </select>
                        </div>
                        <input type="hidden" name="customer_ids" id="customer_ids" value="">

                        <button type="submit" class="btn btn-dark">Create Campaign</button>
                    </form>
                </div>
                <div class="col-md-5" id="customer-list-container" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h4>Customers</h4>
                        </div>
                        <div class="card-body" id="customer-list">
                            <!-- Customer list will be populated here via AJAX -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@section('scripts')
    <script>
        function toggleCustomerList() {
            const targetGroup = document.getElementById('target_group').value;
            const partnerId = document.getElementById('partner_id').value;
            const customerListContainer = document.getElementById('customer-list-container');
            console.log(targetGroup, partnerId);
            if (targetGroup !== '' && partnerId !== null) {
                customerListContainer.style.display = 'block';
                fetchCustomerList(targetGroup, partnerId);
            } else {
                customerListContainer.style.display = 'none';
                document.getElementById('customer-list').innerHTML = ''; // Clear the list
            }
        }

        function fetchCustomerList(targetGroup, partnerId) {
            var url = '{{ route('sms-campaigns.customers', [':targetGroup', ':partnerId']) }}';
            url = url.replace(':targetGroup', targetGroup).replace(':partnerId', partnerId);

            fetch(url) // Adjust this route as necessary
                .then(response => response.json())
                .then(data => {
                    const customerListElement = document.getElementById('customer-list');
                    customerListElement.innerHTML = ''; // Clear previous list
                    const customerIds = []; // Array to hold customer IDs

                    // Create a table structure
                    const table = document.createElement('table');
                    table.classList.add('table', 'table-bordered'); // Add Bootstrap classes for styling

                    // Create table header
                    const headerRow = document.createElement('tr');
                    const header1 = document.createElement('th');
                    header1.textContent = 'Name';
                    const header2 = document.createElement('th');
                    header2.textContent = 'Phone';

                    headerRow.appendChild(header1);
                    headerRow.appendChild(header2);
                    table.appendChild(headerRow);

                    // Populate table rows with customer data
                    if (data.length > 0) {
                        data.forEach(customer => {
                            const row = document.createElement('tr');
                            const cell1 = document.createElement('td');
                            if (customer.First_Name && customer.Last_Name) {
                                cell1.textContent = `${customer.First_Name} ${customer.Last_Name}`;
                            }
                            if (customer.name) {
                                cell1.textContent = customer.name;
                            }
                            const cell2 = document.createElement('td');
                            if (customer.Telephone_Number) {
                                cell2.textContent = customer.Telephone_Number;
                            }
                            if (customer.phone) {
                                cell2.textContent = customer.phone;
                            }
                            row.appendChild(cell1);
                            row.appendChild(cell2);
                            table.appendChild(row);

                            customerIds.push(customer.id);
                            console.log(customerIds);

                        });
                    } else {
                        const row = document.createElement('tr');
                        const cell = document.createElement('td');
                        cell.colSpan = 2; // Span across two columns
                        cell.textContent = 'No customers found.';
                        row.appendChild(cell);
                        table.appendChild(row);
                    }

                    // Append the table to the customer list element
                    customerListElement.appendChild(table);
                    document.getElementById('customer_ids').value = JSON.stringify(customerIds);
                })
                .catch(error => {
                    console.error('Error fetching customer list:', error);
                });
        }
    </script>
@endsection
@endsection
