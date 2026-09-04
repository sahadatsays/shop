@php
    use App\Support\MoneyFormatter;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $purchase->purchase_number }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; color: #111; margin: 2rem; }
        h1 { margin: 0 0 .25rem; font-size: 1.5rem; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        th, td { border-bottom: 1px solid #ddd; padding: .6rem .4rem; text-align: left; font-size: .9rem; }
        th { text-transform: uppercase; font-size: .7rem; letter-spacing: .04em; color: #666; }
        .totals { margin-top: 1.5rem; max-width: 20rem; margin-left: auto; }
        .totals div { display: flex; justify-content: space-between; padding: .35rem 0; }
        .grand { font-weight: 700; border-top: 1px solid #111; margin-top: .5rem; padding-top: .5rem; }
        @media print { button { display: none; } }
    </style>
</head>
<body>
    <button onclick="window.print()" style="margin-bottom: 1rem;">Print</button>
    <h1>{{ $purchase->purchase_number }}</h1>
    <p class="muted">Purchase order · {{ $purchase->status->label() }}</p>
    <p>
        <strong>Supplier:</strong> {{ $purchase->supplier?->name }}<br>
        <strong>Date:</strong> {{ $purchase->purchase_date?->format('M j, Y') }}<br>
        <strong>Expected delivery:</strong> {{ $purchase->expected_delivery_date?->format('M j, Y') ?: '—' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Qty</th>
                <th>Unit cost</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->items as $item)
                <tr>
                    <td>{{ $item->product_name_snapshot }}</td>
                    <td>{{ $item->sku_snapshot }}</td>
                    <td>{{ $item->quantity_ordered }}</td>
                    <td>{{ MoneyFormatter::format($item->unit_cost_cents) }}</td>
                    <td>{{ MoneyFormatter::format($item->subtotal_cents) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span class="muted">Subtotal</span><span>{{ MoneyFormatter::format($purchase->subtotal_cents) }}</span></div>
        <div><span class="muted">Discount</span><span>{{ MoneyFormatter::format($purchase->discount_cents) }}</span></div>
        <div><span class="muted">Shipping</span><span>{{ MoneyFormatter::format($purchase->shipping_cents) }}</span></div>
        <div><span class="muted">Tax</span><span>{{ MoneyFormatter::format($purchase->tax_cents) }}</span></div>
        <div class="grand"><span>Grand total</span><span>{{ MoneyFormatter::format($purchase->grand_total_cents) }}</span></div>
    </div>
</body>
</html>
