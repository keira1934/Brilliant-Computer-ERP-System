@extends('layouts.app')
@php $title = 'PO #' . $purchase->po_number; @endphp
@section('content')
<div class="page-header">
    <div><h1>{{ $purchase->po_number }}</h1><p class="page-sub">{{ $purchase->supplier->name }} — {{ $purchase->purchase_date->format('d F Y') }}</p></div>
    <div class="page-header-actions">
        <span class="{{ $purchase->getStatusBadgeClass() }}" style="font-size:13px;padding:6px 14px">{{ $purchase->status }}</span>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="grid-2 mb-5">
    <div class="card">
        <div class="card-header"><h5 class="card-title">PO Details</h5></div>
        <div class="card-body">
            <table style="width:100%;font-size:13.5px">
                <tr><td style="padding:5px 0;color:var(--gray-500);width:130px">PO Number</td><td class="font-mono fw-semibold">{{ $purchase->po_number }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Supplier</td><td class="fw-semibold">{{ $purchase->supplier->name }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">PO Date</td><td>{{ $purchase->purchase_date->format('d M Y') }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Expected By</td><td>{{ $purchase->expected_date?->format('d M Y') ?? 'Not specified' }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Total Value</td><td class="fw-bold" style="font-size:16px;color:var(--navy-900)">Rp {{ number_format($purchase->total,0,',','.') }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Notes</td><td>{{ $purchase->notes ?? '-' }}</td></tr>
            </table>
        </div>
    </div>

    @if(!in_array($purchase->status, ['Received', 'Paid', 'Cancelled'], true))
    <div class="card" style="border-left: 4px solid var(--success)">
        <div class="card-header"><h5 class="card-title"><i class="bi bi-box-arrow-in-down" style="color:var(--success);margin-right:8px"></i>Receive Goods</h5></div>
        <div class="card-body">
            @if($purchase->status === 'Pending Approval')
            <p style="font-size:13.5px;color:var(--gray-600);margin-bottom:16px">
                This PO is waiting for manager approval before receiving can proceed.
            </p>
            @if(auth()->user()->isManager())
            <form method="POST" action="{{ route('purchases.approve', $purchase) }}" style="margin-bottom:12px">
                @csrf
                <button class="btn btn-primary" onclick="return confirm('Approve PO {{ $purchase->po_number }}?')"><i class="bi bi-check2-circle"></i> Approve PO</button>
            </form>
            @endif
            @else
            <p style="font-size:13.5px;color:var(--gray-600);margin-bottom:16px">
                Confirming receipt will update inventory stock and post journal entries automatically:<br>
                <span class="font-mono" style="font-size:12px;background:var(--navy-50);padding:4px 10px;border-radius:4px;display:inline-block;margin-top:8px">
                    Dr Inventory &nbsp;/&nbsp; Cr Accounts Payable &nbsp;(then)&nbsp; Dr AP &nbsp;/&nbsp; Cr Cash
                </span>
            </p>
            <button type="button"
                onclick="postAction('{{ route('purchases.receive', $purchase) }}', 'Confirm receipt of goods for PO {{ $purchase->po_number }}? This will update stock and post the journal entry.')"
                class="btn btn-success">
                <i class="bi bi-box-arrow-in-down"></i> Confirm Goods Received
            </button>
            @endif
        </div>
    </div>
    @elseif(in_array($purchase->status, ['Received', 'Paid'], true))
    <div class="alert alert-success" style="align-self:start">
        <i class="bi bi-check-circle-fill"></i>
        <span><strong>Goods Received.</strong> Stock, AP invoice, and journal entry posted automatically.</span>
    </div>
    @endif
</div>

@if($purchase->apInvoices->count())
<div class="card mb-5">
    <div class="card-header"><h5 class="card-title">Payable Link</h5></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Invoice</th><th>Status</th><th class="text-right">Total</th><th class="text-right">Outstanding</th><th></th></tr></thead>
            <tbody>
            @foreach($purchase->apInvoices as $invoice)
            <tr>
                <td class="font-mono td-primary">{{ $invoice->invoice_number }}</td>
                <td><span class="badge {{ $invoice->status === 'Paid' ? 'badge-success' : 'badge-warning' }}">{{ $invoice->status }}</span></td>
                <td class="text-right">Rp {{ number_format($invoice->total,0,',','.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($invoice->outstanding,0,',','.') }}</td>
                <td><a href="{{ route('accounts-payable.show', $invoice) }}" class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header"><h5 class="card-title">Order Items</h5></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Product</th><th>SKU</th><th class="text-right">Qty</th><th class="text-right">Unit Cost</th><th class="text-right">Total</th></tr></thead>
            <tbody>
            @foreach($purchase->items as $i => $item)
            <tr>
                <td class="td-muted">{{ $i+1 }}</td>
                <td class="td-primary">{{ $item->product->name }}</td>
                <td class="font-mono td-muted">{{ $item->product->sku }}</td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_cost,0,',','.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($item->total,0,',','.') }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--navy-50)">
                    <td colspan="5" class="text-right fw-bold" style="padding:12px 16px;color:var(--navy-900)">TOTAL</td>
                    <td class="text-right fw-bold" style="padding:12px 16px;color:var(--navy-900);font-size:15px">Rp {{ number_format($purchase->total,0,',','.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
