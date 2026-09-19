<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    /**
     * Step 1: Send WhatsApp OTP and check if user exists
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $phone = substr(preg_replace('/[^0-9]/', '', $request->phone), -10);

        if (strlen($phone) !== 10) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please enter a valid 10-digit WhatsApp phone number.'
            ], 422);
        }

        // Check if customer exists in the database
        $existingUser = User::where('phone', $phone)
            ->orWhere('email', "{$phone}@patsonfoods.local")
            ->first();

        // Reset previous OTP session
        Session::forget(['verification_otp', 'otp_expires_at', 'temp_phone']);

        $otp = rand(1000, 9999);

        // Store OTP & metadata in session (10-minute expiry)
        Session::put('verification_otp', (string) $otp);
        Session::put('temp_phone', $phone);
        Session::put('otp_expires_at', now()->addMinutes(10));
        Session::save();

        $adminPhone = config('services.whatsapp.admin_number', '917758943614');

        $greetingName = $existingUser ? $existingUser->name : 'Valued Customer';
        $message = "🌴 *PatSons Foods Goa - Login Verification*\n";
        $message .= "-----------------------------------\n";
        $message .= "👤 *Account:* {$greetingName}\n";
        $message .= "📱 *WhatsApp:* +91 {$phone}\n";
        $message .= "🔑 *Your 4-Digit Verification Code:* *{$otp}*\n";
        $message .= "⏱️ *Valid for:* 10 Minutes\n";
        $message .= "-----------------------------------\n";
        $message .= "Enter this code on the website to verify your account.";

        $whatsappUrl = "https://wa.me/{$adminPhone}?text=" . urlencode($message);

        return response()->json([
            'status' => 'success',
            'is_registered' => (bool) $existingUser,
            'existing_user' => $existingUser ? [
                'name' => $existingUser->name,
                'address' => $existingUser->address
            ] : null,
            'whatsapp_url' => $whatsappUrl,
            'test_otp' => $otp,
            'message' => 'OTP generated.'
        ]);
    }

    /**
     * Step 2: Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        $enteredOtp = trim($request->otp);
        $sessionOtp = Session::get('verification_otp');
        $expiresAt = Session::get('otp_expires_at');
        $phone = Session::get('temp_phone');

        if (!$sessionOtp || !$expiresAt || now()->isAfter($expiresAt)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Verification code expired. Please request a new code.'
            ], 422);
        }

        if ($enteredOtp !== (string) $sessionOtp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid 4-digit code. Please check and re-enter.'
            ], 422);
        }

        // Authenticate phone session
        Session::put('customer_phone', $phone);
        Session::forget(['verification_otp', 'otp_expires_at']);

        // Check if user already has a saved profile
        $user = User::where('phone', $phone)
            ->orWhere('email', "{$phone}@patsonfoods.local")
            ->first();

        if ($user && !empty($user->name)) {
            Session::put('customer_name', $user->name);
            Session::save();

            return response()->json([
                'status' => 'success',
                'is_registered' => true,
                'customer_name' => $user->name,
                'delivery_note' => $user->address ?? '',
                'customer_phone' => $phone
            ]);
        }

        // Needs initial registration (First-Time User)
        return response()->json([
            'status' => 'success',
            'is_registered' => false,
            'customer_phone' => $phone
        ]);
    }

    /**
     * Step 3: Complete First-Time Profile Setup
     */
    public function saveProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
        ]);

        $phone = session('customer_phone') ?? substr(preg_replace('/[^0-9]/', '', $request->phone), -10);

        if (!$phone) {
            return response()->json(['status' => 'error', 'message' => 'Session expired. Please re-verify phone.'], 401);
        }

        $user = User::updateOrCreate(
            ['phone' => $phone],
            [
                'name' => trim($request->name),
                'email' => "{$phone}@patsonfoods.local",
                'address' => trim($request->address),
                'password' => bcrypt(Str::random(16)),
                'is_admin' => 0
            ]
        );

        Session::put('customer_name', $user->name);
        Session::save();

        return response()->json([
            'status' => 'success',
            'customer_name' => $user->name,
            'delivery_note' => $user->address,
            'customer_phone' => $phone
        ]);
    }
}