@extends('layouts.app')
@php $title = 'New Sale'; @endphp
@section('content')
<div class="page-header">
    <div><h1>New Sales Transaction</h1></div>
    <a href="{{ route('sales.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="POST" action="{{ route('sales.store') }}">
@csrf
<div class="grid-2 mb-5" style="align-items:start">
    <div class="card">
        <div class="card-header"><h5 class="card-title">Transaction Info</h5></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Customer</label>
                <select name="customer_id" class="form-control">
                    <option value="">-- Walk-in / No Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }} — {{ $c->phone }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Sale Date <span class="required">*</span></label>
                <input name="sale_date" type="date" value="{{ old('sale_date', date('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Method <span class="required">*</span></label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="Cash"     {{ old('payment_method','Cash')==='Cash'    ?'selected':'' }}>Cash</option>
                    <option value="Transfer" {{ old('payment_method')==='Transfer'?'selected':'' }}>Bank Transfer</option>
                    <option value="Other"    {{ old('payment_method')==='Other'   ?'selected':'' }}>Other</option>
                </select>
            </div>
            <div class="form-row">
                <label class="form-check" style="display:flex;gap:8px;align-items:center;margin-top:4px">
                    <input type="checkbox" name="is_credit_sale" value="1" {{ old('is_credit_sale') ? 'checked' : '' }}>
                    <span>Invoice on credit / collect later</span>
                </label>
                <div class="form-group">
                    <label class="form-label">Terms Days</label>
                    <input name="payment_terms_days" type="number" min="0" max="365" value="{{ old('payment_terms_days',30) }}" class="form-control">
                </div>
            </div>
            <div id="payment-other-wrap" style="display:none">
                <div class="form-group">
                    <label class="form-label">Payment Description <span class="required">*</span></label>
                    <input name="payment_notes" value="{{ old('payment_notes') }}" class="form-control" placeholder="e.g. Debit Card, QRIS, etc.">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Optional order notes">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Order Summary</h5>
        </div>
        <div class="card-body">
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px dashed var(--gray-200)">
                <span class="td-muted">Subtotal</span>
                <span id="display-subtotal" class="fw-semibold">Rp 0</span>
            </div>
            <div style="padding:10px 0">
                <label class="form-label" style="margin-bottom:4px">Discount (Rp)</label>
                <input id="discount" name="discount" type="number" min="0" value="{{ old('discount',0) }}" class="form-control">
            </div>
            <div style="display:flex;justify-content:space-between;padding:12px 0;font-size:18px;font-weight:800;color:var(--navy-900);border-top:2px solid var(--navy-200)">
                <span>Total</span>
                <span id="display-total">Rp 0</span>
            </div>
            <input type="hidden" name="subtotal" id="hidden-subtotal">
            <input type="hidden" name="total" id="hidden-total">
            <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-check-lg"></i> Save Sale & Post Journal</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Sale Items</h5>
        <button type="button" id="add-item-btn" class="btn btn-outline btn-sm"><i class="bi bi-plus-lg"></i> Add Item</button>
    </div>
    <div class="card-body" style="padding:0">
        <table style="width:100%">
            <thead><tr>
                <th style="padding:10px 14px;background:var(--navy-100);color:var(--navy-700);font-size:11px;font-weight:700;text-transform:uppercase">Product</th>
                <th style="padding:10px 14px;background:var(--navy-100);color:var(--navy-700);font-size:11px;width:90px">Qty</th>
                <th style="padding:10px 14px;background:var(--navy-100);color:var(--navy-700);font-size:11px;width:160px">Unit Price</th>
                <th style="padding:10px 14px;background:var(--navy-100);color:var(--navy-700);font-size:11px;width:150px">Line Total</th>
                <th style="padding:10px 8px;background:var(--navy-100);width:36px"></th>
            </tr></thead>
            <tbody id="item-rows">
            <tr class="item-row">
                <td style="padding:10px 12px">
                    <select name="items[0][product_id]" class="form-control product-select" required>
                        <option value="">-- Select Product --</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->sell_price }}">{{ $p->name }} (Stock: {{ $p->stock }})</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding:10px 12px"><input name="items[0][qty]" type="number" min="1" value="1" class="form-control qty-input" required></td>
                <td style="padding:10px 12px"><input name="items[0][unit_price]" class="form-control price-input" type="number" min="0" placeholder="0" required></td>
                <td style="padding:10px 12px"><input type="text" class="form-control line-total" readonly style="background:var(--gray-50)"></td>
                <td style="padding:10px 6px"><button type="button" class="btn btn-sm btn-danger btn-icon remove-item"><i class="bi bi-x"></i></button></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<template id="item-row-template">
<tr class="item-row">
    <td style="padding:10px 12px">
        <select name="items[__IDX__][product_id]" class="form-control product-select" required>
            <option value="">-- Select Product --</option>
            @foreach($products as $p)
            <option value="{{ $p->id }}" data-price="{{ $p->sell_price }}">{{ $p->name }} (Stock: {{ $p->stock }})</option>
            @endforeach
        </select>
    </td>
    <td style="padding:10px 12px"><input name="items[__IDX__][qty]" type="number" min="1" value="1" class="form-control qty-input" required></td>
    <td style="padding:10px 12px"><input name="items[__IDX__][unit_price]" class="form-control price-input" type="number" min="0" placeholder="0" required></td>
    <td style="padding:10px 12px"><input type="text" class="form-control line-total" readonly style="background:var(--gray-50)"></td>
    <td style="padding:10px 6px"><button type="button" class="btn btn-sm btn-danger btn-icon remove-item"><i class="bi bi-x"></i></button></td>
</tr>
</template>
</form>
@endsection
