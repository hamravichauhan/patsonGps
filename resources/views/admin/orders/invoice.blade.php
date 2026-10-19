<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan & Invoice #{{ $order->order_number }} | PatSons Foods Goa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
                max-width: 100% !important;
            }
            @page {
                size: A4;
                margin: 12mm;
            }
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-stone-100 text-gray-900 min-h-screen p-4 sm:p-8">

    <!-- Action Bar (Hidden when printing) -->
    <div class="max-w-4xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 no-print">
        <a href="{{ route('admin.orders') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-bold text-xs rounded-xl shadow-xs transition">
            <span>←</span>
            <span>Back to Orders</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-900 hover:bg-green-800 text-white font-black text-xs rounded-xl shadow-md transition cursor-pointer">
                <span>🖨️</span>
                <span>Print Delivery Challan / Invoice</span>
            </button>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}?text={{ urlencode('Hello ' . $order->customer_name . '! Here is the delivery challan summary for your PatSons Goa Order #' . $order->order_number . '. Grand Total: ₹' . number_format($order->total_amount, 2) . '. Our delivery van is ready.') }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs rounded-xl shadow-md transition">
                <span>💬</span>
                <span>WhatsApp Summary</span>
            </a>
        </div>
    </div>

    <!-- Main Printable Invoice Sheet -->
    <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-2xl shadow-xl p-8 sm:p-12 invoice-card">

        <!-- Top Header & Letterhead -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pb-6 border-b-2 border-green-950">
            <div class="space-y-1.5">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo1.webp') }}" alt="PatSons Logo"
                        onerror="this.src='{{ asset('images/logo.png') }}'"
                        class="h-14 w-auto object-contain">
                    <div>
                        <h1 class="text-2xl font-black text-green-950 tracking-tight">DEV CAN FRUIT PRODUCTS</h1>
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-widest">Brand: PatSons Foods Goa</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed pt-2">
                    Morjim, Pernem, Goa — 403512, India<br>
                    <strong>FSSAI Central / State Lic. No.:</strong> 10613001000730<br>
                    <strong>GSTIN:</strong> 30BAGPP8577A1ZS &bull; <strong>State Code:</strong> 30 (Goa)<br>
                    <strong>Phone / WhatsApp:</strong> +91 7758943614 &bull; <strong>Email:</strong> info@patsonsfoods.com
                </p>
            </div>

            <div class="text-left sm:text-right space-y-1 shrink-0">
                <span class="inline-block bg-green-950 text-white font-black text-[11px] uppercase tracking-wider px-3.5 py-1.5 rounded-lg shadow-xs">
                    Delivery Challan & Tax Invoice
                </span>
                <div class="pt-2">
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Invoice Reference</span>
                    <span class="text-base font-black text-gray-900 font-mono">#{{ $order->order_number }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Date of Issue</span>
                    <span class="text-xs font-bold text-gray-800">{{ $order->created_at ? $order->created_at->format('d M, Y • h:i A') : date('d M, Y') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Dispatch Status</span>
                    <span class="text-xs font-black uppercase text-emerald-800">● {{ $order->status }}</span>
                </div>
            </div>
        </div>

        <!-- Billed & Shipped To -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-gray-200 text-xs">
            <div class="space-y-1 bg-stone-50/70 p-4 rounded-xl border border-stone-200">
                <span class="text-[10px] uppercase tracking-wider font-black text-gray-500 block mb-1">Billed To (Customer Details):</span>
                <h3 class="text-sm font-black text-gray-900">{{ $order->customer_name }}</h3>
                <p class="text-gray-700 font-medium">WhatsApp: <strong>+91 {{ $order->customer_phone }}</strong></p>
                <p class="text-gray-600">Place of Supply: <strong>Goa (State Code 30)</strong></p>
            </div>

            <div class="space-y-1 bg-stone-50/70 p-4 rounded-xl border border-stone-200">
                <span class="text-[10px] uppercase tracking-wider font-black text-gray-500 block mb-1">Delivered To / Delivery Destination:</span>
                <p class="text-gray-900 font-bold leading-relaxed whitespace-pre-line">{{ $order->delivery_address ?: 'Morjim, Pernem - Goa' }}</p>
                <p class="text-gray-500 text-[11px] pt-1">Dispatch Mode: <strong>Factory Van / Express Local Courier</strong></p>
            </div>
        </div>

        <!-- Itemized Products Table -->
        <div class="py-6">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50 text-[10px] uppercase tracking-wider text-gray-600 font-black">
                        <th class="py-3 px-3 w-10 text-center">#</th>
                        <th class="py-3 px-3">Description of Goan Products</th>
                        <th class="py-3 px-3">Size / Pack</th>
                        <th class="py-3 px-3 text-center">HSN</th>
                        <th class="py-3 px-3 text-center w-16">Qty</th>
                        <th class="py-3 px-3 text-right">Unit Rate (₹)</th>
                        <th class="py-3 px-3 text-right">Taxable Amount (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-800">
                    @php 
                        $totalQty = 0;
                        $calcSubtotal = 0;
                    @endphp
                    @forelse($order->items as $idx => $item)
                        @php
                            $qty = (int) ($item->quantity ?? 1);
                            $rate = (float) ($item->unit_price ?? 0);
                            $lineSubtotal = (float) ($item->subtotal ?? ($rate * $qty));
                            $totalQty += $qty;
                            $calcSubtotal += $lineSubtotal;
                            
                            // Approximate HSN: 2007 for jams/fruit pastes, 2106 for syrups/preparations
                            $hsn = stripos($item->product_name, 'Jam') !== false ? '2007' : '2106';
                        @endphp
                        <tr>
                            <td class="py-3 px-3 text-center font-bold text-gray-400">{{ $idx + 1 }}</td>
                            <td class="py-3 px-3">
                                <span class="font-black text-gray-900 block">{{ $item->product_name }}</span>
                                <span class="text-[10px] text-gray-400">Authentic Dev Can Fruit Products</span>
                            </td>
                            <td class="py-3 px-3 font-medium">{{ $item->size ?: 'Standard' }}</td>
                            <td class="py-3 px-3 text-center text-gray-500 font-mono">{{ $hsn }}</td>
                            <td class="py-3 px-3 text-center font-black">{{ $qty }}</td>
                            <td class="py-3 px-3 text-right font-medium">₹{{ number_format($rate, 2) }}</td>
                            <td class="py-3 px-3 text-right font-bold text-gray-900">₹{{ number_format($lineSubtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-400">No line items recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals & Taxes Breakdown -->
        <div class="border-t-2 border-gray-200 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-6 items-start">
            <div class="space-y-3 text-xs text-gray-600 bg-stone-50 p-4 rounded-xl border border-stone-200">
                <div>
                    <span class="font-black text-gray-800 block text-[11px] uppercase">Bank & UPI Settlement Details:</span>
                    <p class="pt-1 text-[11px] leading-relaxed">
                        Account Name: <strong>Dev Can Fruit Products</strong><br>
                        Bank: <strong>State Bank of India (SBI), Pernem Branch</strong><br>
                        UPI ID: <strong>7758943614@okbizaxis</strong> (or scan on delivery)
                    </p>
                </div>
                <div class="border-t border-stone-200 pt-2 text-[10px] text-gray-500">
                    Terms: Goods dispatched are non-returnable once seals are opened. Certified that the particulars above are true and correct.
                </div>
            </div>

            <div class="space-y-2 text-xs">
                @php
                    $grandTotal = (float) ($order->total_amount > 0 ? $order->total_amount : $calcSubtotal);
                    // Standard GST breakdown (assuming inclusive or 5% / 12% composition)
                    $taxable = round($grandTotal / 1.05, 2);
                    $totalTax = round($grandTotal - $taxable, 2);
                    $cgst = round($totalTax / 2, 2);
                    $sgst = round($totalTax / 2, 2);
                @endphp
                <div class="flex justify-between py-1 text-gray-600">
                    <span>Total Quantity:</span>
                    <span class="font-bold text-gray-900">{{ $totalQty }} unit(s) / box(es)</span>
                </div>
                <div class="flex justify-between py-1 text-gray-600">
                    <span>Taxable Value (Est):</span>
                    <span class="font-medium">₹{{ number_format($taxable, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 text-gray-600">
                    <span>CGST @ 2.5%:</span>
                    <span class="font-medium">₹{{ number_format($cgst, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 text-gray-600">
                    <span>SGST @ 2.5%:</span>
                    <span class="font-medium">₹{{ number_format($sgst, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-t-2 border-green-950 text-base font-black text-green-950">
                    <span>Grand Total (INR):</span>
                    <span>₹{{ number_format($grandTotal, 2) }}</span>
                </div>
                <div class="text-[10px] text-right text-gray-400 font-bold uppercase">
                    (Indian Rupees {{ number_format($grandTotal, 2) }} Only)
                </div>
            </div>
        </div>

        <!-- Signatures & Acceptance Box -->
        <div class="mt-12 pt-8 border-t border-gray-200 grid grid-cols-2 gap-8 text-xs text-center">
            <div class="space-y-12">
                <p class="text-gray-400 uppercase text-[10px] font-bold">Receiver's Signature / Seal</p>
                <div class="border-t border-dashed border-gray-300 pt-2 font-medium text-gray-700">
                    Received Goods in Good Condition
                </div>
            </div>

            <div class="space-y-12">
                <p class="text-gray-400 uppercase text-[10px] font-bold">For DEV CAN FRUIT PRODUCTS</p>
                <div class="border-t border-dashed border-gray-300 pt-2 font-black text-green-950">
                    Authorized Signatory
                </div>
            </div>
        </div>

    </div>

</body>
</html>
