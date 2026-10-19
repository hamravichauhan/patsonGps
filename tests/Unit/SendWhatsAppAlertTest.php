<?php

namespace Tests\Unit;

use App\Jobs\SendWhatsAppAlert;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Tests\TestCase;

class SendWhatsAppAlertTest extends TestCase
{
    public function test_send_whatsapp_alert_job_calls_whatsapp_service_successfully(): void
    {
        $mockService = Mockery::mock(WhatsAppService::class);
        $mockService->shouldReceive('sendMessage')
            ->once()
            ->with('919876543210', 'Test message alert')
            ->andReturn(true);

        $job = new SendWhatsAppAlert('919876543210', 'Test message alert');
        $job->handle($mockService);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals(5, $job->backoff);
    }

    public function test_send_whatsapp_alert_job_throws_exception_on_failure_for_retry(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('WhatsApp Gateway delivery failed for 919876543210');

        $mockService = Mockery::mock(WhatsAppService::class);
        $mockService->shouldReceive('sendMessage')
            ->once()
            ->with('919876543210', 'Failed message')
            ->andReturn(false);

        $job = new SendWhatsAppAlert('919876543210', 'Failed message');
        $job->handle($mockService);
    }

    public function test_whatsapp_service_queue_message_dispatches_after_response(): void
    {
        Bus::fake([SendWhatsAppAlert::class]);

        $service = new WhatsAppService();
        $service->queueMessage('919876543210', 'Async queued message');

        Bus::assertDispatchedAfterResponse(SendWhatsAppAlert::class, function ($job) {
            return $job->phone === '919876543210' && $job->message === 'Async queued message';
        });
    }
}
