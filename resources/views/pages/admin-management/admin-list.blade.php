@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0"><span class="text-muted">Admin Management</span> <span class="mx-2">/</span>
                All
                Admin Management</h5>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <table id="category_list" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)

                    <tr>
                        <td><span
                                class="dt-name">{{$user->first_name .' ' }}{{($user->last_name != null) ? $user->last_name : '' }}</span>
                            <br>
                            <a href="tel:{{$user->phone}}" class="dt-name fw-normal">{{$user->phone}}</a><br>
                            <a href="mailto:{{$user->email}}" class="dt-name fw-normal">{{$user->email}}</a>
                        </td>
                        <td>{{ucfirst(str_replace('_',' ', $user->rolesData?->name))}}</td>
                        <td>{{ucfirst($user->branch?->name)}}</td>
                        <td>
                            @if($user->status == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>

                            <a href="{{route('admin.edit', $user->id)}}"
                               title="Edit"
                               class="btn btn-sm btn-text-secondary rounded-pill btn-icon"
                            >
                                <i class="mdi mdi-pencil-outline" style="color: #ff8000;"></i>
                            </a>
                            <a href="#"
                               title="Delete"
                               class="btn btn-sm btn-text-secondary rounded-pill btn-icon"
                               onclick="deleteUser({{$user->id}})"
                            >
                                <i class="mdi mdi-delete" style="color: #800000"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#category_list').DataTable(
                {
                    dom: '<"d-flex justify-content-between"lfB>rtip',
                    buttons: [{
                        extend: 'collection',
                        className: 'btn btn-outline-secondary dropdown-toggle',
                        text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span>Export</span>',
                        buttons: [
                            {
                                extend: 'print',
                                text: '<i class="mdi mdi-printer-outline me-1"></i>Print',
                                className: 'dropdown-item',
                                exportOptions: {columns: [0, 1, 2]}
                            },
                            {
                                extend: 'csv',
                                text: '<i class="mdi mdi-file-document-outline me-1"></i>CSV',
                                className: 'dropdown-item',
                                exportOptions: {columns: [0, 1, 2, 3]}
                            },
                            {
                                extend: 'excel',
                                text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
                                className: 'dropdown-item',
                                exportOptions: {columns: [0, 1, 2, 3]}
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="mdi mdi-file-pdf-box me-1"></i>PDF',
                                className: 'dropdown-item',
                                exportOptions: {columns: [0, 1, 2, 3]}
                            }
                        ]
                    },

                        {
                            text: 'Add Category',
                            className: 'btn btn-info',
                            action: function (e, dt, node, config) {
                                window.location.href = './admin/create';
                            }
                        }
                    ]
                },
            )

        });
    </script>
    <script>
        function deleteUser(id) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content')
            Swal.fire({
                title: "Are you sure?",
                text: "You will not be able to recover this Category!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms, proceed with the AJAX delete call
                    $.ajax({
                        url: '/admin/destroy/' + id,
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            Swal.fire({
                                title: "Done!",
                                text: "Category deleted Successfully!!",
                                icon: "success",
                                showCancelButton: false,
                                confirmButtonText: "OK",
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
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
@endpush
