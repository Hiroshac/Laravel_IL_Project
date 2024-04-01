@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Product</div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">Image:</label>
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" style="max-width: 200px;">
                            @else
                                <p>No image available</p>
                            @endif
                            <input type="file" name="image" id="image" class="form-control-file">
                        </div>

                        <div class="form-group">
                            <label>Categories:</label><br>
                            @foreach ($allCategories as $category)
                                <div class="form-check form-check-inline">
                                    <!-- <input class="form-check-input" type="checkbox" name="categories[]" id="category{{ $category->id }}" value="{{ $category->id }}" {{ $product->categories->contains($category->id) ? 'checked' : '' }}> -->
                                    <input class="form-check-input" type="checkbox" name="categories[]" id="category{{ $category->id }}" value="{{ $category->id }}" {{ in_array($category->id, $productCategories) ? 'checked="checked"' : '' }}>
                                    <label class="form-check-label" for="category{{ $category->id }}">{{ $category->name }}</label>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection