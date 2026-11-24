<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('whatsapp:check', function () {
    $apiUrl = config('services.whatsapp.api_url', 'http://127.0.0.1:21465/api/patsons-session');
    $healthUrl = preg_replace('#/api/.*$#', '/health', $apiUrl);

    try {
        $res = \Illuminate\Support\Facades\Http::timeout(5)->get($healthUrl);
        if ($res->successful()) {
            $data = $res->json();
            $connected = $data['connected'] ?? false;
            $state = $data['state'] ?? 'UNKNOWN';
            if ($connected) {
                $this->info("✓ WhatsApp Gateway is active & connected (Session: {$data['session']})");
                return 0;
            }
            $this->warn("! WhatsApp Gateway is running but session state is: {$state}");
            return 1;
        }
        $this->error("✗ WhatsApp Gateway returned HTTP " . $res->status());
        return 1;
    } catch (\Throwable $e) {
        $this->error("✗ WhatsApp Gateway is offline/unreachable: " . $e->getMessage());
        return 1;
    }
})->purpose('Verify live connection and session state of the WhatsApp Web Gateway');