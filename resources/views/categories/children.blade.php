@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Category: {{ $category->name }}</div>

                <div class="card-body">
                    <h5>Description:</h5>
                    <p>{{ $category->description ?: 'No description available' }}</p>

                    <hr>

                    @if ($children->isNotEmpty())
                        <h5>Sub Categories:</h5>
                        <ul class="list-group">
                            @foreach ($children as $child)
                                <li class="list-group-item">{{ $child->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No Sub categories found.</p>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('categories.addChild', $category->id) }}" class="btn btn-primary">Add Sub Category</a>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back to Categories</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection