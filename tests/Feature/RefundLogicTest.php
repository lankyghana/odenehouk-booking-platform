<?php

namespace Tests\Feature;

use App\Enums\PaymentState;
use App\Models\Payment;
use App\Services\PaymentTransitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class RefundLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_partial_refund_keeps_payment_refundable(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'succeeded',
            'amount' => 100,
            'refunded_amount' => 20,
        ]);

        $this->assertTrue($payment->fresh()->canBeRefunded());
    }

    public function test_double_refund_attempt_throws_invalid_transition(): void
    {
        $service = app(PaymentTransitionService::class);

        $payment = Payment::factory()->create([
            'status' => 'succeeded',
            'amount' => 100,
            'refunded_amount' => 100,
        ]);

        $service->transition($payment, PaymentState::REFUNDED);

        $this->expectException(RuntimeException::class);
        $service->transition($payment->fresh(), PaymentState::FAILED);
    }
}
