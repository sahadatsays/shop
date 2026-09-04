@php
    use App\Support\MoneyFormatter;

    $store = $snapshot['store'] ?? [];
    $customer = $snapshot['customer'] ?? [];
    $totals = $snapshot['totals'] ?? [];
    $items = $snapshot['items'] ?? [];
    $payment = $snapshot['payment'] ?? [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->invoice_number }} — Invoice</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, sans-serif; color: #0f172a; background: #f8fafc; }
        .sheet { max-width: 900px; margin: 24px auto; background: #fff; padding: 40px; border: 1px solid #e2e8f0; }
        .actions { max-width: 900px; margin: 16px auto 0; display: flex; gap: 8px; }
        .btn { appearance: none; border: 1px solid #cbd5e1; background: #fff; padding: 8px 14px; border-radius: 8px; text-decoration: none; color: #0f172a; font-size: 14px; cursor: pointer; }
        .btn-primary { background: #0f172a; color: #fff; border-color: #0f172a; }
        h1 { margin: 0; font-size: 28px; }
        .muted { color: #64748b; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #e2e8f0; text-align: left; font-size: 14px; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
        .totals { margin-left: auto; width: 280px; margin-top: 16px; }
        .totals div { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .totals .grand { font-weight: 700; border-top: 1px solid #e2e8f0; margin-top: 8px; padding-top: 10px; }
        @media print {
            body { background: #fff; }
            .actions { display: none !important; }
            .sheet { border: 0; margin: 0; max-width: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button class="btn btn-primary" type="button" onclick="window.print()">Print invoice</button>
        <a class="btn" href="{{ route('admin.orders.show', $order) }}">Back to order</a>
    </div>

    <div class="sheet">
        <div class="grid">
            <div>
                @if (! empty($store['logo_path']))
                    <img src="{{ asset('storage/'.$store['logo_path']) }}" alt="" style="max-height: 56px; margin-bottom: 12px;">
                @endif
                <h1>{{ $store['name'] ?? config('app.name') }}</h1>
                <p class="muted">{{ $store['address'] ?? '' }}</p>
                <p class="muted">{{ $store['phone'] ?? '' }} · {{ $store['email'] ?? '' }}</p>
            </div>
            <div style="text-align: right;">
                <h1>Invoice</h1>
                <p><strong>{{ $invoice->invoice_number }}</strong></p>
                <p class="muted">Order {{ $snapshot['order_number'] ?? $order->order_number }}</p>
                <p class="muted">{{ optional($invoice->issued_at)->format('M j, Y g:i A') }}</p>
            </div>
        </div>

        <div class="grid" style="margin-top: 32px;">
            <div>
                <h3 style="margin: 0 0 8px;">Customer</h3>
                <p style="margin: 0;">{{ $customer['name'] ?? $order->customer->name }}</p>
                <p class="muted" style="margin: 4px 0;">{{ $customer['phone'] ?? $order->customer->phone }}</p>
                <p class="muted" style="margin: 0;">{{ $customer['email'] ?? $order->customer->email }}</p>
            </div>
            <div>
                <h3 style="margin: 0 0 8px;">Ship to</h3>
                <p class="muted" style="margin: 0; white-space: pre-line;">
                    {{ collect($snapshot['shipping_address'] ?? [])->only(['first_name','last_name','line1','line2','city','state','postal_code','country'])->filter()->implode("\n") }}
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Discount</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item['product_name'] ?? 'Product' }}</td>
                        <td>{{ $item['sku'] ?? '—' }}</td>
                        <td>{{ $item['quantity'] ?? 0 }}</td>
                        <td>{{ MoneyFormatter::format((int) ($item['unit_price_cents'] ?? 0)) }}</td>
                        <td>{{ MoneyFormatter::format((int) ($item['discount_cents'] ?? 0)) }}</td>
                        <td>{{ MoneyFormatter::format((int) ($item['line_total_cents'] ?? 0)) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No line items.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals">
            <div><span class="muted">Subtotal</span><span>{{ MoneyFormatter::format((int) ($totals['subtotal_cents'] ?? 0)) }}</span></div>
            <div><span class="muted">Discount</span><span>{{ MoneyFormatter::format((int) ($totals['discount_cents'] ?? 0)) }}</span></div>
            <div><span class="muted">Shipping</span><span>{{ MoneyFormatter::format((int) ($totals['shipping_cents'] ?? 0)) }}</span></div>
            <div><span class="muted">Tax</span><span>{{ MoneyFormatter::format((int) ($totals['tax_cents'] ?? 0)) }}</span></div>
            <div class="grand"><span>Grand total</span><span>{{ MoneyFormatter::format((int) ($totals['total_cents'] ?? 0)) }}</span></div>
            <div><span class="muted">Paid</span><span>{{ MoneyFormatter::format((int) ($totals['paid_cents'] ?? 0)) }}</span></div>
            <div><span class="muted">Due</span><span>{{ MoneyFormatter::format((int) ($totals['due_cents'] ?? 0)) }}</span></div>
            <div><span class="muted">Payment</span><span>{{ $payment['status_label'] ?? '' }} · {{ $payment['method'] ?? '' }}</span></div>
        </div>

        @if (! empty($snapshot['notes']))
            <p style="margin-top: 28px;"><strong>Notes</strong><br>{{ $snapshot['notes'] }}</p>
        @endif

        <p class="muted" style="margin-top: 28px; font-size: 12px;">{{ $snapshot['terms'] ?? '' }}</p>
    </div>
</body>
</html>
