<?php

namespace Tests\Unit;

use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class StripeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_verification_requires_secret(): void
    {
        config(['services.stripe.webhook_secret' => null]);

        $service = app(StripeService::class);

        $this->expectException(RuntimeException::class);
        $service->verifyWebhookSignature('{}', 'sig');
    }
}
