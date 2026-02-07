@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0"><span class="text-muted">Order</span> <span class="mx-2">/</span> All Order</h5>
        </div>
    </div>
    <div class="card">
        <div class="card-datatable table-responsive">
            <table id="order_list" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Order Date</th>
                    <th>Item No/s</th>
                    <th>Customer Details</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#order_list').DataTable({
                processing: true,
                serverSide: true, // Enable server-side processing
                ajax: {
                    url: "{{ route('order.index') }}", // Your route
                    type: 'GET'
                },
                columns: [
                    { data: 'invoice_number', name: 'invoice_number' },
                    { data: 'order_date', name: 'created_at' },
                    { data: 'items', name: 'items', orderable: false, searchable: false },
                    { data: 'customer_details', name: 'customer_details', orderable: false },
                    { data: 'total_amount', name: 'sub_total' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                dom: '<"d-flex justify-content-between align-items-center px-2"lfB>rtip',
                order: [[1, 'desc']], // Order by order_date column
                pageLength: 10, // Show 10 rows per page
                buttons: [
                    {
                        text: 'Add New Order',
                        className: 'btn btn-info me-2',
                        action: function (e, dt, node, config) {
                            window.location.href = '{{ route("order.create") }}';
                        }
                    }
                ]
            });
        });
    </script>

    <!-- Keep your existing deleteOrder and updateStatus scripts -->
    <script>
        function deleteOrder(id) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content')
            Swal.fire({
                title: "Are you sure?",
                text: "You will not be able to recover this Order!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms, proceed with the AJAX delete call
                    $.ajax({
                        url: '/order/destroy/' + id,
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            Swal.fire({
                                title: "Success",
                                text: "Order deleted Successfully!!",
                                icon: "success",
                                // confirmButtonText: "OK",
                                // allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#order_list').DataTable().ajax.reload();
                                }
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching data.');
                            console.log('XHR Status: ' + status);
                            console.log('Error: ' + error); // Log error message
                            console.log(xhr.responseText);
                        }
                    });
                }
            });

        }
    </script>

    <script>
        const statusFlow = {
            1: {next: 2, label: 'In Use', class: 'bg-info'},
            2: {next: 3, label: 'Returned', class: 'bg-success'},
            3: {next: 4, label: 'Cancelled', class: 'bg-secondary'},
            4: {next: 1, label: 'Pending', class: 'bg-warning'}
        };
        function updateStatus(element) {
             const orderId = element.dataset.orderId;
            const currentStatus = parseInt(element.dataset.currentStatus);
            const statusBadge = document.getElementById(`statusBadge${orderId}`);
            const toggle = element;

            // Disable toggle while processing
            toggle.disabled = true;

            const newStatus = statusFlow[currentStatus];

            if (newStatus) {
                // Make API request to update status
                fetch('/order/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        orderId: orderId,
                        newStatus: newStatus.next
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Update UI only after successful API response
                        statusBadge.textContent = newStatus.label;
                        statusBadge.className = `badge ${newStatus.class}`;
                        element.dataset.currentStatus = newStatus.next;

                        // Re-enable toggle
                        toggle.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error updating status:', error);
                        // Revert toggle state on error
                        toggle.checked = !toggle.checked;
                        toggle.disabled = false;

                        // Show error message to user
                        alert('Failed to update status. Please try again.');
                    });
            }
        }
    </script>
@endpush
