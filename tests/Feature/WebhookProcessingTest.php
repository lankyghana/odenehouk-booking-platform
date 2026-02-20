<?php

namespace Tests\Feature;

use App\Models\StripeEvent;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Event;
use Tests\TestCase;

class WebhookProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_webhook_is_deduplicated(): void
    {
        $service = app(StripeService::class);

        $event = [
            'id' => 'evt_duplicate_1',
            'type' => 'payment_intent.created',
            'data' => ['object' => ['id' => 'pi_xxx']],
        ];

        $service->processWebhookEvent($event);
        $service->processWebhookEvent($event);

        $this->assertDatabaseCount('stripe_events', 1);
        $this->assertDatabaseHas('stripe_events', [
            'stripe_event_id' => 'evt_duplicate_1',
            'status' => 'processed',
        ]);
    }

    public function test_invalid_signature_returns_400(): void
    {
        $this->mock(StripeService::class, function ($mock): void {
            $mock->shouldReceive('verifyWebhookSignature')->once()->andThrow(new \RuntimeException('invalid sig'));
        });

        $response = $this->postJson(route('webhook.stripe'), [], [
            'Stripe-Signature' => 'invalid',
        ]);

        $response->assertStatus(400);
    }
}
