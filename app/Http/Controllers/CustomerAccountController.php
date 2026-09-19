<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CustomerAccountController extends Controller
{
    /**
     * Display customer order history and active session.
     */
    public function index(Request $request)
    {
        $phone = session('customer_phone');
        $customerName = session('customer_name');

        if (!$phone) {
            return view('pages.my-account', [
                'orders' => collect(),
                'phone' => null,
                'customerName' => null,
            ]);
        }

        $orders = Order::with('items')
            ->where('customer_phone', $phone)
            ->latest()
            ->get();

        return view('pages.my-account', [
            'orders' => $orders,
            'phone' => $phone,
            'customerName' => $customerName,
        ]);
    }

    /**
     * Standard phone login fallback.
     */
    public function loginWithPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $cleanPhone = substr(preg_replace('/[^0-9]/', '', $request->phone), -10);

        if (strlen($cleanPhone) !== 10) {
            return back()->withErrors(['phone' => 'Please enter a valid 10-digit WhatsApp phone number.']);
        }

        $request->session()->regenerate();
        session(['customer_phone' => $cleanPhone]);

        $user = User::where('phone', $cleanPhone)
            ->orWhere('email', "{$cleanPhone}@patsonfoods.local")
            ->first();

        if ($user && !empty($user->name)) {
            session(['customer_name' => $user->name]);
        } else {
            $pastOrder = Order::where('customer_phone', $cleanPhone)->whereNotNull('customer_name')->latest()->first();
            if ($pastOrder && !empty($pastOrder->customer_name)) {
                session(['customer_name' => $pastOrder->customer_name]);
            }
        }

        return redirect()->route('customer.account');
    }

    /**
     * Send a 4-Digit OTP to customer WhatsApp with 60s cooldown.
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
                'message' => 'Please provide a valid 10-digit WhatsApp number.',
            ], 422);
        }

        // 60-Second Cooldown Check
        $cooldownKey = 'otp_cooldown_' . $cleanPhone;
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please wait 60 seconds before requesting another code.',
            ], 429);
        }

        // Generate 4-digit OTP (cached for 10 minutes)
        $otp = (string) rand(1000, 9999);
        Cache::put('track_otp_' . $cleanPhone, $otp, now()->addMinutes(10));
        Cache::put($cooldownKey, true, now()->addSeconds(60));
        Cache::put('otp_attempts_' . $cleanPhone, 0, now()->addMinutes(10));

        $msg = "🔐 *PatSons Foods Goa - Track Order Verification*\n\n"
            . "Your 4-digit verification code is: *{$otp}*\n\n"
            . "Use this code to view your orders and quotation history. Valid for 10 minutes.";

        $waService->sendMessage($cleanPhone, $msg);

        return response()->json([
            'status' => 'success',
            'message' => 'A 4-digit code has been sent to your WhatsApp.',
        ]);
    }

    /**
     * Verify 4-Digit OTP with attempt throttling.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
            'otp' => 'required|string|size:4',
        ]);

        $cleanPhone = substr(preg_replace('/[^0-9]/', '', $request->phone), -10);
        $cachedOtp = Cache::get('track_otp_' . $cleanPhone);
        $attemptKey = 'otp_attempts_' . $cleanPhone;
        $attempts = (int) Cache::get($attemptKey, 0);

        // Max 5 attempts protection
        if ($attempts >= 5) {
            Cache::forget('track_otp_' . $cleanPhone);
            return response()->json([
                'status' => 'error',
                'message' => 'Too many failed attempts. Please request a new verification code.',
            ], 429);
        }

        if (!$cachedOtp || $cachedOtp !== trim($request->otp)) {
            Cache::increment($attemptKey);
            $remaining = max(0, 4 - $attempts);
            return response()->json([
                'status' => 'error',
                'message' => "Invalid or expired OTP. ({$remaining} attempts remaining)",
            ], 422);
        }

        // Clean cache & regenerate session
        Cache::forget('track_otp_' . $cleanPhone);
        Cache::forget($attemptKey);
        Cache::forget('otp_cooldown_' . $cleanPhone);

        $request->session()->regenerate();
        session(['customer_phone' => $cleanPhone]);

        $user = User::where('phone', $cleanPhone)
            ->orWhere('email', "{$cleanPhone}@patsonfoods.local")
            ->first();

        if ($user && !empty($user->name)) {
            session(['customer_name' => $user->name]);
        } else {
            $pastOrder = Order::where('customer_phone', $cleanPhone)->whereNotNull('customer_name')->latest()->first();
            if ($pastOrder && !empty($pastOrder->customer_name)) {
                session(['customer_name' => $pastOrder->customer_name]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Authentication successful.',
            'redirect_url' => route('customer.account'),
        ]);
    }

    /**
     * Log out and clear customer session.
     */
    public function logout(Request $request)
    {
        session()->forget(['customer_phone', 'customer_name', 'verified_customer_phone']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.account');
    }

    /**
     * API endpoint to retrieve past order items for 1-click reorder.
     */
    public function loadReorder($orderNumber)
    {
        try {
            $order = Order::with('items')->where('order_number', $orderNumber)->first();

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order #' . $orderNumber . ' could not be found.',
                ], 404);
            }

            $cartItems = [];

            // 1. Relational OrderItem records
            if ($order->items && $order->items->count() > 0) {
                foreach ($order->items as $item) {
                    $cartItems[] = [
                        'key' => ($item->product_catalog_id ?? $item->id) . '-' . ($item->size ?? 'Standard'),
                        'id' => $item->product_catalog_id ?? $item->id,
                        'name' => $item->product_name,
                        'size' => $item->size ?? 'Standard',
                        'price' => (float) $item->unit_price,
                        'quantity' => max(1, (int) $item->quantity),
                    ];
                }
            }

            // 2. Fallback to order_items JSON column
            if (empty($cartItems) && !empty($order->order_items)) {
                $decoded = is_string($order->order_items)
                    ? json_decode($order->order_items, true)
                    : $order->order_items;

                if (is_array($decoded)) {
                    foreach ($decoded as $item) {
                        $cartItems[] = [
                            'key' => ($item['id'] ?? 'item') . '-' . ($item['size'] ?? 'Standard'),
                            'id' => $item['id'] ?? null,
                            'name' => $item['name'] ?? 'Product',
                            'size' => $item['size'] ?? 'Standard',
                            'price' => (float) ($item['price'] ?? 0),
                            'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                        ];
                    }
                }
            }

            if (empty($cartItems)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No line items available to reorder for this quotation.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'cart_items' => $cartItems,
            ]);

        } catch (\Throwable $e) {
            Log::error('Reorder load error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve order items.',
            ], 500);
        }
    }
}