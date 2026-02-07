@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0">
                <span class="text-muted">Item</span>
                <span class="mx-2">/</span>
                All Item
            </h5>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <table id="item_list" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Item No</th>
                        <th>Item Name</th>
                        <th>Item Category</th>
                        <th>Item Colour</th>
                        <th>Material</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#item_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('item.index') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'coat_no', name: 'coat_no' },
                    { data: 'name', name: 'name' },
                    { data: 'category_name', name: 'category.name' },
                    { data: 'color_display', name: 'color', orderable: false, searchable: false },
                    { data: 'material', name: 'material' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                dom: '<"d-flex justify-content-between align-items-center px-2"lfB>rtip',
                order: [[0, 'desc']],
                buttons: [
                    {
                        text: 'Add New Item',
                        className: 'btn btn-info me-2',
                        action: function (e, dt, node, config) {
                            window.location.href = "{{ route('item.create') }}";
                        }
                    }
                ]
            });

            // Delete item handler
            $(document).on('click', '.delete-item', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this Item!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/item/destroy/' + id,
                            type: 'DELETE', // Changed to DELETE method
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function (data) {
                                Swal.fire({
                                    title: "Done!",
                                    text: "Item deleted Successfully!!",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    allowOutsideClick: false
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $('#item_list').DataTable().ajax.reload();
                                    }
                                });
                            },
                            error: function (xhr, status, error) {
                                console.error('Error deleting item.');
                                console.log('XHR Status: ' + status);
                                console.log('Error: ' + error);
                                console.log(xhr.responseText);

                                Swal.fire({
                                    title: "Error!",
                                    text: "Failed to delete item. Please try again.",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
