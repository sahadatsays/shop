@php
    use App\Support\MoneyFormatter;
    use App\Support\StoreSettings;

    $storeSettings = StoreSettings::current();
    $store = [
        'name' => $storeSettings->store_name,
        'address' => $storeSettings->address,
        'phone' => $storeSettings->phone,
        'email' => $storeSettings->support_email ?: $storeSettings->email,
        'website' => config('app.url'),
        'tagline' => $storeSettings->tagline ?: 'Quality Products. Reliable Service. Fast Delivery.',
    ];

    $supplier = $purchase->supplier;
    $grandTotalCents = (int) $purchase->grand_total_cents;

    if (!function_exists('purchaseNumberToWords')) {
        function purchaseNumberToWords(int $cents): string
        {
            $taka = (int) floor($cents / 100);
            $poisha = $cents % 100;

            if ($taka <= 0 && $poisha <= 0) {
                return 'Zero Taka Only.';
            }

            if (class_exists(\NumberFormatter::class)) {
                $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
                $words = ucwords($formatter->format($taka)) . ' Taka';
                if ($poisha > 0) {
                    $words .= ' And ' . ucwords($formatter->format($poisha)) . ' Poisha';
                }
                return $words . ' Only.';
            }

            return number_format($taka) . ' Taka Only.';
        }
    }

    $amountInWords = purchaseNumberToWords($grandTotalCents);
    $purchaseDate = $purchase->purchase_date ? $purchase->purchase_date->format('d F Y') : now()->format('d F Y');
    $expectedDelivery = $purchase->expected_delivery_date ? $purchase->expected_delivery_date->format('d F Y') : '—';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $purchase->purchase_number }} — Purchase Order</title>
    <style>
        :root {
            color-scheme: light;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1e293b;
            background: #f1f5f9;
            padding: 20px 0;
            font-size: 13px;
            line-height: 1.5;
        }

        .actions-bar {
            max-width: 850px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            transition: all 0.15s;
        }

        .btn:hover {
            background: #f8fafc;
        }

        .btn-primary {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
        }

        .btn-primary:hover {
            background: #0369a1;
        }

        .invoice-sheet {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Header Section */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .company-brand {
            font-size: 24px;
            font-weight: 800;
            color: #0f2942;
            text-transform: uppercase;
            letter-spacing: -0.01em;
            margin: 0 0 4px;
            line-height: 1.1;
        }

        .company-tagline {
            font-size: 12px;
            color: #64748b;
            margin: 0 0 6px;
            font-weight: 500;
        }

        .company-meta {
            font-size: 12px;
            color: #475569;
            margin: 2px 0;
        }

        .header-right {
            text-align: right;
        }

        .invoice-title {
            font-size: 34px;
            font-weight: 800;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin: 0 0 10px;
            line-height: 1;
        }

        .header-meta-table {
            font-size: 12px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .header-meta-table td {
            padding: 2px 0 2px 10px;
            text-align: right;
        }

        .header-meta-table td.label {
            font-weight: 700;
            color: #0f172a;
        }

        .header-meta-table td.value {
            color: #475569;
        }

        .header-divider {
            border: none;
            height: 2px;
            background: #0284c7;
            margin: 16px 0 24px;
        }

        /* Reference / Address Box */
        .info-box {
            border: 1px solid #b8c4d0;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .info-box th {
            background: #f0f4f8;
            padding: 8px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            border-bottom: 1px solid #b8c4d0;
            text-align: left;
            width: 50%;
        }

        .info-box td {
            padding: 14px;
            vertical-align: top;
            font-size: 12px;
            border-right: 1px solid #b8c4d0;
            width: 50%;
            line-height: 1.6;
        }

        .info-box td:last-child {
            border-right: none;
        }

        .info-box th:last-child {
            border-right: none;
        }

        .info-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            border: 1px solid #b8c4d0;
        }

        .items-table th {
            background: #0f2942;
            color: #ffffff;
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: 1px solid #0f2942;
            text-align: left;
        }

        .items-table th.text-center {
            text-align: center;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table td {
            padding: 12px;
            border: 1px solid #b8c4d0;
            font-size: 13px;
            color: #1e293b;
            vertical-align: middle;
        }

        .items-table td.text-center {
            text-align: center;
        }

        .items-table td.text-right {
            text-align: right;
        }

        .item-sku {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Summary Section */
        .summary-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .summary-wrapper {
            width: 340px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .summary-table td {
            padding: 5px 0;
            font-size: 13px;
        }

        .summary-table td.label {
            color: #475569;
            font-weight: 500;
        }

        .summary-table td.value {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }

        .total-due-box {
            background: #0284c7;
            color: #ffffff;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-due-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            line-height: 1.2;
        }

        .total-due-amount {
            font-size: 18px;
            font-weight: 800;
        }

        /* Footer Notes & Words */
        .words-section {
            font-size: 13px;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .notes-section {
            font-size: 11px;
            color: #64748b;
            font-style: italic;
            margin-bottom: 40px;
            line-height: 1.4;
        }

        /* Signature Section */
        .signature-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 60px;
        }

        .signature-block {
            width: 240px;
            text-align: center;
        }

        .signature-svg {
            margin-bottom: -12px;
        }

        .signature-line {
            border-top: 1px dashed #94a3b8;
            margin-bottom: 6px;
        }

        .signature-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .signature-subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0 0;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .actions-bar {
                display: none !important;
            }

            .invoice-sheet {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="actions-bar">
        <div>
            <a class="btn" href="{{ route('admin.purchases.show', $purchase) }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Back to Purchase
            </a>
        </div>
        <div>
            <button class="btn btn-primary" type="button" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9" />
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                    <rect x="6" y="14" width="12" height="8" />
                </svg>
                Print Purchase Order
            </button>
        </div>
    </div>

    <div class="invoice-sheet">
        <!-- Header -->
        <div class="invoice-header">
            <div>
                <h1 class="company-brand">{{ $store['name'] }}</h1>
                <p class="company-tagline">{{ $store['tagline'] }}</p>
                @if (!empty($store['address']))
                    <p class="company-meta">Head Office: {{ $store['address'] }}</p>
                @endif
                <p class="company-meta">
                    @if (!empty($store['email']))
                        Email: {{ $store['email'] }}
                    @endif
                    @if (!empty($store['email']) && !empty($store['website']))
                        |
                    @endif
                    @if (!empty($store['website']))
                        Website: {{ parse_url($store['website'], PHP_URL_HOST) ?: $store['website'] }}
                    @endif
                    @if (!empty($store['phone']))
                        | Phone: {{ $store['phone'] }}
                    @endif
                </p>
            </div>
            <div class="header-right">
                <h2 class="invoice-title">PURCHASE BILL</h2>
                <table class="header-meta-table">
                    <tr>
                        <td class="label">PO No:</td>
                        <td class="value">{{ $purchase->purchase_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date:</td>
                        <td class="value">{{ $purchaseDate }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status:</td>
                        <td class="value">{{ $purchase->status->label() }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr class="header-divider">

        <!-- Info Box (Supplier Info & Purchase Details) -->
        <table class="info-box">
            <thead>
                <tr>
                    <th>SUPPLIER INFO</th>
                    <th>PURCHASE DETAILS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="info-name">{{ $supplier?->name ?? 'Supplier' }}</div>
                        @if ($supplier?->company_name)
                            <div><strong>Company:</strong> {{ $supplier->company_name }}</div>
                        @endif
                        @if ($supplier?->address)
                            <div>{!! nl2br(e($supplier->address)) !!}</div>
                        @endif
                        @if ($supplier?->phone)
                            <div>Contact: {{ $supplier->phone }}</div>
                        @endif
                        @if ($supplier?->email)
                            <div>Email: {{ $supplier->email }}</div>
                        @endif
                    </td>
                    <td>
                        <div><strong>PO Number:</strong> {{ $purchase->purchase_number }}</div>
                        <div><strong>Purchase Date:</strong> {{ $purchaseDate }}</div>
                        <div><strong>Expected Delivery:</strong> {{ $expectedDelivery }}</div>
                        <div><strong>Status:</strong> {{ $purchase->status->label() }}</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Line Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45px;" class="text-center">SL</th>
                    <th>DESCRIPTION</th>
                    <th style="width: 70px;" class="text-center">QTY</th>
                    <th style="width: 70px;" class="text-center">UNIT</th>
                    <th style="width: 140px;" class="text-right">AMOUNT (BDT)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchase->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ sprintf('%02d', $index + 1) }}</td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">{{ $item->product_name_snapshot }}</div>
                            @if (!empty($item->sku_snapshot))
                                <div class="item-sku">SKU: {{ $item->sku_snapshot }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity_ordered }}</td>
                        <td class="text-center">Pcs</td>
                        <td class="text-right">{{ number_format($item->subtotal_cents / 100, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 20px; color: #64748b;">No line items
                            found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary & Total Box -->
        <div class="summary-container">
            <div class="summary-wrapper">
                <table class="summary-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">{{ number_format($purchase->subtotal_cents / 100, 2) }}</td>
                    </tr>
                    @if ($purchase->discount_cents > 0)
                        <tr>
                            <td class="label">Discount</td>
                            <td class="value">-{{ number_format($purchase->discount_cents / 100, 2) }}</td>
                        </tr>
                    @endif
                    @if ($purchase->shipping_cents > 0)
                        <tr>
                            <td class="label">Shipping</td>
                            <td class="value">{{ number_format($purchase->shipping_cents / 100, 2) }}</td>
                        </tr>
                    @endif
                    @if ($purchase->tax_cents > 0)
                        <tr>
                            <td class="label">Tax</td>
                            <td class="value">{{ number_format($purchase->tax_cents / 100, 2) }}</td>
                        </tr>
                    @endif
                </table>

                <div class="total-due-box">
                    <div class="total-due-title">TOTAL DUE<br>(BDT)</div>
                    <div class="total-due-amount">{{ number_format($grandTotalCents / 100, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Amount in words & Notes -->
        <div class="words-section">
            Amount in words: <strong>{{ $amountInWords }}</strong>
        </div>

        <div class="notes-section">
            Note: This purchase order has been generated officially. Please mention the PO number in all correspondence.
            @if (!empty($purchase->notes))
                <br><strong>Notes:</strong> {{ $purchase->notes }}
            @endif
        </div>

        <!-- Signatures -->
        <div class="signature-container">
            <div class="signature-block">
                {{-- <div style="height: 35px; display: flex; align-items: flex-end; justify-content: center;">
                    <svg class="signature-svg" width="120" height="35" viewBox="0 0 120 35" fill="none">
                        <path d="M10 25C25 10 40 30 55 15C70 0 80 32 95 18C105 8 110 22 115 12" stroke="#0f2942" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div> --}}
                <div class="signature-line"></div>
                <div class="signature-title">Authorized Signature</div>
                <div class="signature-subtitle">{{ $store['name'] }}</div>
            </div>

            <div class="signature-block">
                <div style="height: 35px;"></div>
                <div class="signature-line"></div>
                <div class="signature-title">Received & Accepted By</div>
                <div class="signature-subtitle">{{ $supplier?->name ?? 'Supplier Representative' }}</div>
            </div>
        </div>
    </div>

</body>

</html>
