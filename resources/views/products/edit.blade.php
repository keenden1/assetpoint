@extends('layouts.app')

@section('title', 'Edit product')

@section('content')
    <div class="mx-auto" style="max-width: 720px;">
        <a href="{{ route('products.index') }}" class="text-decoration-none small text-secondary d-inline-block mb-2">
            <i class="bi bi-arrow-left me-1"></i>Back to products
        </a>
        <h1 class="h3 mb-4 page-title">Edit product</h1>

        <div class="card card-elevated">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('products.update', $product) }}" novalidate>
                    @method('PUT')
                    @include('products._form')
                </form>
            </div>
        </div>
    </div>
@endsection
