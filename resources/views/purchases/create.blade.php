@extends('layouts.app')
@php $title = 'Buat Order Pembelian'; $breadcrumb = 'Pembelian / PO Baru'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Buat Order Pembelian</h1></div>
    <a href="{{ route('purchases.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<form method="POST" action="{{ route('purchases.store') }}">
@csrf
<div class="grid-2 mb-5" style="align-items:start">
    <div class="card">
        <div class="card-header"><h5 class="card-title">Informasi PO</h5></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Supplier <span class="required">*</span></label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ old('supplier_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal PO <span class="required">*</span></label>
                    <input name="purchase_date" type="date" value="{{ old('purchase_date', date('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Est. Tiba</label>
                    <input name="expected_date" type="date" value="{{ old('expected_date') }}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Buat Order Pembelian</button>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Item Pembelian</h5>
            <button type="button" id="add-item-btn" class="btn btn-outline btn-sm"><i class="bi bi-plus-lg"></i> Tambah Item</button>
        </div>
        <div class="card-body" style="padding:0">
            <table style="width:100%">
                <thead><tr>
                    <th style="padding:10px 14px;background:var(--navy-100);color:var(--navy-700);font-size:11px;font-weight:700;text-transform:uppercase">Produk</th>
                    <th style="padding:10px 14px;background:var(--navy-100);color:var(--navy-700);font-size:11px;width:90px">Qty</th>
                    <th style="padding:10px 14px;background:var(--navy-100);color:var(--navy-700);font-size:11px;width:150px">Harga Beli</th>
                    <th style="padding:10px 14px;background:var(--navy-100);color:var(--navy-700);font-size:11px;width:140px">Total</th>
                    <th style="padding:10px 8px;background:var(--navy-100);width:36px"></th>
                </tr></thead>
                <tbody id="item-rows">
                <tr class="item-row">
                    <td style="padding:10px 12px">
                        <select name="items[0][product_id]" class="form-control product-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->cost_price }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding:10px 12px"><input name="items[0][qty]" type="number" min="1" value="1" class="form-control qty-input" required></td>
                    <td style="padding:10px 12px"><input name="items[0][unit_cost]" class="form-control price-input" type="number" min="0" placeholder="0" required></td>
                    <td style="padding:10px 12px"><input type="text" class="form-control line-total" readonly style="background:var(--gray-50)"></td>
                    <td style="padding:10px 6px"><button type="button" class="btn btn-sm btn-danger btn-icon remove-item"><i class="bi bi-x"></i></button></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</form>

<template id="item-row-template">
<tr class="item-row">
    <td style="padding:10px 12px">
        <select name="items[__IDX__][product_id]" class="form-control product-select" required>
            <option value="">-- Pilih Produk --</option>
            @foreach($products as $p)
            <option value="{{ $p->id }}" data-price="{{ $p->cost_price }}">{{ $p->name }}</option>
            @endforeach
        </select>
    </td>
    <td style="padding:10px 12px"><input name="items[__IDX__][qty]" type="number" min="1" value="1" class="form-control qty-input" required></td>
    <td style="padding:10px 12px"><input name="items[__IDX__][unit_cost]" class="form-control price-input" type="number" min="0" placeholder="0" required></td>
    <td style="padding:10px 12px"><input type="text" class="form-control line-total" readonly style="background:var(--gray-50)"></td>
    <td style="padding:10px 6px"><button type="button" class="btn btn-sm btn-danger btn-icon remove-item"><i class="bi bi-x"></i></button></td>
</tr>
</template>
@endsection
