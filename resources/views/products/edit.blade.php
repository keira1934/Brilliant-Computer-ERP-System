@extends('layouts.app')
@php $title = 'Edit Product'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Edit Product</h1></div>
    <a href="{{ route('products.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">SKU <span class="required">*</span></label>
                    <input name="sku" value="{{ $product->sku }}" class="form-control @error('sku') is-invalid @enderror" readonly required>
                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Category <span class="required">*</span></label>
                    <select name="category" class="form-control" required>
                        @foreach(['Laptop','Printer','CPU','Accessories','Other'] as $c)
                        <option value="{{ $c }}" {{ old('category',$product->category)==$c?'selected':'' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Product Name <span class="required">*</span></label>
                <input name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Cost Price (Rp)</label>
                    <input name="cost_price" type="number" min="0" value="{{ old('cost_price', $product->cost_price) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Selling Price (Rp)</label>
                    <input name="sell_price" type="number" min="0" value="{{ old('sell_price', $product->sell_price) }}" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Current Stock</label>
                    <input name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Minimum Stock</label>
                    <input name="min_stock" type="number" min="0" value="{{ old('min_stock', $product->min_stock) }}" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Unit</label>
                <input name="unit" value="{{ old('unit', $product->unit) }}" class="form-control" style="max-width:160px">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
