@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0">
                <a href="{{ route('item.index') }}" class="text-muted text-decoration-underline" style="cursor:pointer;">Item</a>
                <span class="mx-2">/</span> Add Item
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4 mb-md-0">
            <div class="card">
                <div class="card-body p-0">
                    <form action="{{route('item.store')}}" method="post" id="create-item-form"
                          enctype="multipart/form-data"
                          autocomplete="off">
                        @csrf
                        <div class="px-3 py-4">

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name"
                                           placeholder="Enter the item name">
                                    <span id="nameError" class="text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Material</label>
                                    <input type="text" class="form-control" name="material"
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Item Color</label>
                                    <input type="color" class="form-control" name="color"
                                           placeholder="Select the color">

                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Add Image</label>
                                    <input type="file" class="form-control" name="file"
                                           accept="image/png, image/jpeg, image/jpg">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Cost</label>
                                    <input type="number" class="form-control" name="cost"
                                           placeholder="Enter the item purchase cost">
                                    <span id="costError" class="text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Size</label>
                                    <input type="text" class="form-control" name="size"
                                           placeholder="Enter the Size">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Item Category</label>
                                    <select class="form-select select2" id="item_category_id" name="item_category_id"
                                            required>
                                        <option selected disabled>Select Category</option>
                                        @foreach($itemCategories as $itemCategory)
                                            <option value="{{$itemCategory->id}}">{{$itemCategory->name}}</option>

                                        @endforeach
                                    </select>
                                    <span id="itemCategoryError" class="text-danger"></span>

                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" name="description"
                                           placeholder="Enter the Description">
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="submit" class="btn btn-success" value="Submit">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#create-item-form').on('submit', function (event) {
                event.preventDefault();  // Prevent the default form submission
                // Send the form data via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        showAlert('success', response.success, "/item");

                    },
                    error: function (response) {
                        if (response.status === 422) {  // Laravel validation error code
                            let errors = response.responseJSON.errors;

                            if (errors.name) {
                                $('#nameError').text(errors.name[0]);
                            }
                            if (errors.cost) {
                                $('#costError').text(errors.cost[0]);
                            }
                            if (errors.item_category_id) {
                                $('#itemCategoryError').text(errors.item_category_id[0]);
                            }

                        }
                    }
                });
            });
        });
    </script>
@endpush
