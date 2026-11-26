<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PricingService
{
    /**
     * Determine if current visitor is an approved wholesaler or admin.
     */
    public static function isWholesaler(): bool
    {
        // 1. Authenticated staff/user check
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_wholesaler || $user->is_admin) {
                return true;
            }
        }

        // 2. Customer portal session check
        $phone = session('customer_phone');
        if (!empty($phone)) {
            $cleanPhone = substr(preg_replace('/[^0-9]/', '', $phone), -10);
            if (!empty($cleanPhone)) {
                $user = User::where('phone', 'like', "%{$cleanPhone}%")->first();
                if ($user && ($user->is_wholesaler || $user->is_admin)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get effective unit price for a product based on current user's pricing tier.
     * Returns wholesale rate for approved wholesalers, MRP for everyone else.
     */
    public static function getEffectivePrice($product): float
    {
        if (self::isWholesaler()) {
            return (float) $product->price;
        }

        return (float) ($product->mrp ?: $product->price);
    }

    /**
     * Get effective box pack price based on pricing tier.
     */
    public static function getEffectiveBoxPrice($product): float
    {
        $units = (int) ($product->units_per_box ?: 12);
        return round(self::getEffectivePrice($product) * $units, 2);
    }

    /**
     * Get full carton/box MRP price.
     */
    public static function getBoxMrp($product): float
    {
        $units = (int) ($product->units_per_box ?: 12);
        $mrpUnit = (float) ($product->mrp ?: $product->price);
        return round($mrpUnit * $units, 2);
    }
}
