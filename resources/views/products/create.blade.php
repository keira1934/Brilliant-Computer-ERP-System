@extends('layouts.app')
@php $title = 'Add Product'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Add Product</h1></div>
    <a href="{{ route('products.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">SKU <span class="required">*</span></label>
                    <input name="sku" id="sku" value="{{ old('sku', $autoSku) }}" class="form-control @error('sku') is-invalid @enderror" readonly required placeholder="e.g. LAP-001">
                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Auto-generated from the selected category.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Category <span class="required">*</span></label>
                    <select name="category" id="category" class="form-control" required>
                        @foreach(['Laptop','Printer','CPU','Accessories','Other'] as $c)
                        <option value="{{ $c }}" {{ old('category')==$c?'selected':'' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Product Name <span class="required">*</span></label>
                <input name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Cost Price (Rp) <span class="required">*</span></label>
                    <input name="cost_price" type="number" min="0" value="{{ old('cost_price',0) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Selling Price (Rp) <span class="required">*</span></label>
                    <input name="sell_price" type="number" min="0" value="{{ old('sell_price',0) }}" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Opening Stock <span class="required">*</span></label>
                    <input name="stock" type="number" min="0" value="{{ old('stock',0) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Minimum Stock <span class="required">*</span></label>
                    <input name="min_stock" type="number" min="0" value="{{ old('min_stock',5) }}" class="form-control" required>
                    <div class="form-text">Alert shows when stock ≤ this value</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Unit</label>
                <input name="unit" value="{{ old('unit','pcs') }}" class="form-control" style="max-width:160px">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
// Auto-update SKU prefix based on category selection
var autoSkus = @json($autoSkus);
document.getElementById('category').addEventListener('change', function() {
    var skuEl = document.getElementById('sku');
    skuEl.value = autoSkus[this.value] || '';
});
</script>
@endpush
