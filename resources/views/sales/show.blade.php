@extends('layouts.app')
@php $title = 'Sale #' . $sale->sale_number; @endphp
@section('content')
<div class="page-header">
    <div><h1>{{ $sale->sale_number }}</h1><p class="page-sub">{{ $sale->sale_date->format('l, d F Y') }}</p></div>
    <div class="page-header-actions">
        <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        <a href="{{ request()->fullUrlWithQuery(['print'=>1]) }}" target="_blank" class="btn btn-outline"><i class="bi bi-printer"></i> Print</a>
    </div>
</div>

<div class="grid-2 mb-5">
    <div class="card">
        <div class="card-header"><h5 class="card-title">Sale Info</h5></div>
        <div class="card-body">
            <table style="width:100%;font-size:13.5px">
                <tr><td style="padding:5px 0;color:var(--gray-500);width:130px">Sale No.</td><td class="font-mono fw-semibold">{{ $sale->sale_number }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Customer</td><td class="fw-semibold">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Date</td><td>{{ $sale->sale_date->format('d M Y') }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Payment</td><td><span class="badge {{ $sale->payment_method === 'Cash' ? 'badge-success' : 'badge-navy' }}">{{ $sale->payment_method }}</span></td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Terms</td><td>{{ $sale->is_credit_sale ? 'Credit - '.$sale->payment_terms_days.' days' : 'Immediate collection' }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Notes</td><td>{{ $sale->notes ?? '-' }}</td></tr>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="card-title">Totals</h5></div>
        <div class="card-body">
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px dashed var(--gray-200)">
                <span class="td-muted">Subtotal</span><span class="fw-semibold">Rp {{ number_format($sale->subtotal,0,',','.') }}</span>
            </div>
            @if($sale->discount > 0)
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px dashed var(--gray-200)">
                <span class="td-muted">Discount</span><span class="text-danger">-Rp {{ number_format($sale->discount,0,',','.') }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;padding:12px 0;font-size:18px;font-weight:800;color:var(--navy-900)">
                <span>Total</span><span>Rp {{ number_format($sale->total,0,',','.') }}</span>
            </div>
            <div class="alert alert-success" style="margin:0;font-size:12.5px">
                <i class="bi bi-check-circle-fill"></i>
                <span>Journal entries auto-posted through AR invoice{{ $sale->is_credit_sale ? '' : ' and collection' }} plus COGS / Inventory.</span>
            </div>
        </div>
    </div>
</div>

@if($sale->arInvoices->count())
<div class="card mb-5">
    <div class="card-header"><h5 class="card-title">Receivable Link</h5></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Invoice</th><th>Status</th><th class="text-right">Total</th><th class="text-right">Outstanding</th><th></th></tr></thead>
            <tbody>
            @foreach($sale->arInvoices as $invoice)
            <tr>
                <td class="font-mono td-primary">{{ $invoice->invoice_number }}</td>
                <td><span class="badge {{ $invoice->status === 'Paid' ? 'badge-success' : 'badge-warning' }}">{{ $invoice->status }}</span></td>
                <td class="text-right">Rp {{ number_format($invoice->total,0,',','.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($invoice->outstanding,0,',','.') }}</td>
                <td><a href="{{ route('accounts-receivable.show', $invoice) }}" class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header"><h5 class="card-title">Sold Items</h5></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Product</th><th class="text-right">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Unit Cost</th><th class="text-right">Line Total</th></tr></thead>
            <tbody>
            @foreach($sale->items as $i => $item)
            <tr>
                <td class="td-muted">{{ $i+1 }}</td>
                <td class="td-primary">{{ $item->product->name }}<br><span class="font-mono td-muted" style="font-size:11px">{{ $item->product->sku }}</span></td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price,0,',','.') }}</td>
                <td class="text-right td-muted">Rp {{ number_format($item->unit_cost,0,',','.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($item->total,0,',','.') }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--navy-50)">
                    <td colspan="5" class="text-right fw-bold" style="padding:12px 16px;color:var(--navy-900)">TOTAL</td>
                    <td class="text-right fw-bold" style="padding:12px 16px;color:var(--navy-900);font-size:15px">Rp {{ number_format($sale->total,0,',','.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
