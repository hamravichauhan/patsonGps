<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $token;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', env('WHATSAPP_API_URL', 'http://127.0.0.1:21465/api/patsons-session'));
        $this->token = config('services.whatsapp.secret_token', env('WHATSAPP_SECRET_TOKEN', 'THISISMYSECURETOKEN'));
    }

    /**
     * Send Background WhatsApp Message Without Page Redirection
     */
    public function sendMessage(string $phone, string $message): bool
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '91' . $cleanPhone;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->timeout(15)->post("{$this->apiUrl}/send-message", [
                        'phone' => "{$cleanPhone}@c.us",
                        'message' => $message,
                    ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent successfully to {$cleanPhone}");
                return true;
            }

            Log::error("WhatsApp Gateway Error: " . $response->body());
            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp Service Exception: ' . $e->getMessage());
            return false;
        }
    }
}