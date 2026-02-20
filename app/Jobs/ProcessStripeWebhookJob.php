<?php

namespace App\Jobs;

use App\Services\StripeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessStripeWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [5, 10, 30, 60, 120];

    public function __construct(public array $eventPayload)
    {
        $this->onQueue('webhooks');
    }

    public function handle(StripeService $stripeService): void
    {
        $stripeService->processWebhookEvent($this->eventPayload);
    }
}
