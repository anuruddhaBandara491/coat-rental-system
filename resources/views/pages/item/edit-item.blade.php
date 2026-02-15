@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0">
                <a href="{{ route('item.index') }}" class="text-muted text-decoration-underline" style="cursor:pointer;">Item</a>
                <span class="mx-2">/</span> Edit Item
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4 mb-md-0">
            <div class="card">
                <div class="card-body p-0">
                    <form action="{{route('item.update',$item->id)}}" method="post" enctype="multipart/form-data"
                          autocomplete="off">
                        @csrf
                        <input type="hidden" name="coat_no" value="{{$item->coat_no}}">
                        <div class="px-3 py-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{$item->name}}"
                                           placeholder="Enter the item name" required>

                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Material</label>
                                    <input type="text" class="form-control" name="material" value="{{$item->material}}"
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Item Color</label>
                                    <input type="color" class="form-control" name="color" value="{{$item->color}}"
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
                                    <input type="number" class="form-control" name="cost" value="{{$item->cost}}"
                                           placeholder="Enter the item purchase cost">

                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Size</label>
                                    <input type="text" class="form-control" name="size" value="{{$item->size}}"
                                           placeholder="Enter the Size">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Item Category</label>
                                    <select class="form-select" name="item_category_id">
                                        @foreach($itemCategories as $category)
                                            <option
                                                value="{{$category->id}}" {{$item->category->id == $category->id ? 'selected' : ''}}>{{$category->name}}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" name="description"
                                           value="{{$item->description}}"
                                           placeholder="Enter the Description">
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="submit" class="btn btn-warning" value="Update">
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
    @if(session('success'))
        <script>
            showAlert('success', "{{ session('success') }}", "/item");

        </script>
    @elseif(session('error'))
        <script>
            showAlert('error', "{{ session('error') }}", "/item");

        </script>
    @endif
@endpush
