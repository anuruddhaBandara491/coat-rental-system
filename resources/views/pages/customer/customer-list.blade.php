@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0">
                <span class="text-muted">Customer</span>
                <span class="mx-2">/</span>
                All Customer
            </h5>
        </div>
    </div>
    <div class="card">
        <div class="card-datatable table-responsive">
            <table id="customer_list" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Customer Full Name</th>
                        <th>Email</th>
                        <th>NIC No.</th>
                        <th>Contact No.</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#customer_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('customer.index') }}",
                columns: [
                    { data: 'full_name', name: 'full_name' },
                    { data: 'email', name: 'email' },
                    { data: 'nic', name: 'nic' },
                    { data: 'phone', name: 'phone' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                dom: '<"d-flex ms-2 justify-content-between align-items-center px-2"lfB>rtip',
                order: [[0, 'desc']],
                buttons: [
                    {
                        text: 'Add Customer',
                        className: 'btn btn-info me-2',
                        action: function (e, dt, node, config) {
                            window.location.href = '{{ route("customer.create") }}';
                        }
                    }
                ]
            });
        });
    </script>

    <script>
        function deleteCustomer(id) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                title: "Are you sure?",
                text: "You will not be able to recover this Customer!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/customer/destroy/' + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (data) {
                            Swal.fire({
                                title: "Done!",
                                text: "Customer deleted Successfully!",
                                icon: "success",
                                confirmButtonText: "OK",
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#customer_list').DataTable().ajax.reload();
                                }
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Error deleting customer.');
                            console.log('XHR Status: ' + status);
                            console.log('Error: ' + error);
                            console.log(xhr.responseText);

                            Swal.fire({
                                title: "Error!",
                                text: "Failed to delete customer.",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
