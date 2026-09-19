<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Step 1: Send 4-Digit OTP in the Background via WhatsApp
     */
    public function sendOtp(Request $request, WhatsAppService $waService)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
        ]);

        $cleanPhone = substr(preg_replace('/[^0-9]/', '', $request->phone), -10);
        if (strlen($cleanPhone) !== 10) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please enter a valid 10-digit WhatsApp number.'
            ], 422);
        }

        // Generate 4-digit code (valid for 10 mins)
        $otp = (string) rand(1000, 9999);
        Cache::put('otp_' . $cleanPhone, $otp, now()->addMinutes(10));

        $msg = "🔐 *PatSons Foods Goa - Security Verification*\n\n"
            . "Your 4-digit verification code is: *{$otp}*\n\n"
            . "Use this OTP to complete your quotation order. Valid for 10 minutes.";

        $waService->sendMessage($cleanPhone, $msg);

        // Check for returning customer details
        $previousOrder = Order::where('customer_phone', $cleanPhone)->latest()->first();
        $isReturning = $previousOrder !== null;
        $existingName = $previousOrder ? $previousOrder->customer_name : '';
        $existingAddress = $previousOrder ? $previousOrder->delivery_address : '';

        return response()->json([
            'status' => 'success',
            'message' => 'A 4-digit verification code has been sent to your WhatsApp.',
            'is_returning' => $isReturning,
            'existing_name' => $existingName,
            'existing_address' => $existingAddress,
        ]);
    }

    /**
     * Step 2: Verify 4-Digit OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
            'otp' => 'required|string|size:4',
        ]);

        $cleanPhone = substr(preg_replace('/[^0-9]/', '', $request->phone), -10);
        $cachedOtp = Cache::get('otp_' . $cleanPhone);

        if (!$cachedOtp || $cachedOtp !== trim($request->otp)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired OTP. Please check and try again.'
            ], 422);
        }

        session(['verified_customer_phone' => $cleanPhone]);

        return response()->json([
            'status' => 'success',
            'message' => 'Phone verified successfully!',
        ]);
    }

    /**
     * Step 3: Save Order to MySQL & Send Confirmation Alerts
     */
    public function store(Request $request, WhatsAppService $waService)
    {
        try {
            $rawPhone = $request->input('customer_phone') ?? session('verified_customer_phone') ?? '';
            $cleanPhone = substr(preg_replace('/[^0-9]/', '', $rawPhone), -10);
            $customerName = trim($request->input('customer_name') ?? 'Valued Customer');
            $deliveryNote = trim($request->input('delivery_address') ?? 'Morjim, Goa');
            $cartItems = $request->input('cart_items', []);

            if (empty($cleanPhone) || strlen($cleanPhone) !== 10) {
                return response()->json(['status' => 'error', 'message' => 'Please provide a valid 10-digit WhatsApp number.'], 422);
            }

            if (empty($cartItems)) {
                return response()->json(['status' => 'error', 'message' => 'Your quotation cart is empty.'], 422);
            }

            // Calculate Totals & Approval
            $totalQuantity = 0;
            foreach ($cartItems as $item) {
                $totalQuantity += isset($item['quantity']) ? max(1, (int) $item['quantity']) : 1;
            }

            $minAutoQty = (int) Setting::get('auto_approval_min_qty', 10);
            $autoApproveEnabled = (bool) Setting::get('auto_approval_enabled', 1);
            $isAutoApproved = $autoApproveEnabled && ($totalQuantity >= $minAutoQty);
            $orderStatus = $isAutoApproved ? 'approved' : 'pending';

            do {
                $orderNumber = 'PAT-' . date('ymd') . '-' . strtoupper(Str::random(4));
            } while (Order::where('order_number', $orderNumber)->exists());

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'customer_phone' => $cleanPhone,
                'delivery_address' => $deliveryNote,
                'total_amount' => 0,
                'status' => $orderStatus,
                'admin_notes' => $isAutoApproved ? "System: Auto-approved (Total Qty {$totalQuantity} >= threshold)." : null,
                'reorder_token' => Str::random(24),
                'order_items' => json_encode($cartItems),
            ]);

            session(['customer_phone' => $cleanPhone]);
            Cache::forget('otp_' . $cleanPhone);

            $totalAmount = 0;
            $itemDescriptions = [];

            foreach ($cartItems as $item) {
                $qty = isset($item['quantity']) ? max(1, (int) $item['quantity']) : 1;
                $price = isset($item['price']) ? (float) $item['price'] : 0.0;
                $subtotal = $price * $qty;
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_catalog_id' => !empty($item['id']) && is_numeric($item['id']) ? $item['id'] : null,
                    'product_name' => $item['name'] ?? 'Goan Product',
                    'size' => $item['size'] ?? 'Standard',
                    'unit_price' => $price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);

                $itemDescriptions[] = "• " . ($item['name'] ?? 'Product') . " (" . ($item['size'] ?? 'Pack') . ") x {$qty} = ₹" . number_format($subtotal, 2);
            }

            $order->update(['total_amount' => $totalAmount]);

            // Background WhatsApp to Customer
            $customerMsg = "🌿 *PatSons Foods Goa - Order Placed!*\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "Hello *{$customerName}*,\n"
                . "Thank you for placing your order quotation with us! We have received your details.\n\n"
                . "📋 *Order Reference:* #{$orderNumber}\n"
                . "📍 *Delivery Address:* {$deliveryNote}\n"
                . "📦 *Total Items:* {$totalQuantity} pcs\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "🛍️ *Your Items:*\n" . implode("\n", $itemDescriptions) . "\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "💰 *Estimated Total:* ₹" . number_format($totalAmount, 2) . "\n\n"
                . "Our factory team is reviewing your order and will confirm delivery shortly.";

            $waService->sendMessage($cleanPhone, $customerMsg);

            // Background WhatsApp to Admin
            $rawAdminPhone = Setting::get('whatsapp_admin_number', config('services.whatsapp.admin_number', '917758943614'));
            $adminPhone = preg_replace('/[^0-9]/', '', $rawAdminPhone);

            $adminMsg = "🔔 *NEW ORDER RECEIVED - PatSons Website*\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "📋 *Order Number:* #{$orderNumber}\n"
                . "👤 *Customer Name:* {$customerName}\n"
                . "📞 *Phone Number:* +91 {$cleanPhone}\n"
                . "📍 *Delivery Address:* {$deliveryNote}\n"
                . "📦 *Total Units:* {$totalQuantity} pcs\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "🛍️ *Item Breakdown:*\n" . implode("\n", $itemDescriptions) . "\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "💵 *Grand Total:* ₹" . number_format($totalAmount, 2);

            $waService->sendMessage($adminPhone, $adminMsg);

            return response()->json([
                'status' => 'success',
                'order_number' => $orderNumber,
                'order_status' => $orderStatus,
                'message' => "Order #{$orderNumber} placed successfully! Confirmation sent to +91 {$cleanPhone}.",
                'redirect_url' => route('customer.account'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Order Store Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save order: ' . $e->getMessage()], 500);
        }
    }
}