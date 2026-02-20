<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(StripeService $stripeService): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'queue' => $this->checkQueue(),
            'stripe' => $this->checkStripe($stripeService),
        ];

        $status = collect($checks)->every(fn (bool $passed) => $passed) ? 'ok' : 'degraded';

        return response()->json([
            'status' => $status,
            'checks' => $checks,
        ], $status === 'ok' ? 200 : 503);
    }

    private function checkDatabase(): bool
    {
        try {
            DB::select('select 1');
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function checkQueue(): bool
    {
        try {
            Queue::connection()->size('default');
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function checkStripe(StripeService $stripeService): bool
    {
        try {
            return $stripeService->checkConnectivity();
        } catch (Throwable) {
            return false;
        }
    }
}
