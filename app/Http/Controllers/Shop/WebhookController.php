<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessXenditWebhook;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk menerima webhook dari Xendit.
 *
 * Endpoint ini harus segera mengembalikan HTTP 200
 * dan mendispatch proses validasi ke Job Queue agar tidak timeout.
 */
class WebhookController extends Controller
{
    /**
     * Handle Xendit webhook callback.
     *
     * Xendit mengirim notifikasi via POST ke endpoint ini saat status
     * invoice berubah (PAID, EXPIRED, dll).
     *
     * POST /payment/webhook
     */
    public function handle(Request $request)
    {
        // ── 1. Verifikasi webhook token ──────────────────────────────────
        $callbackToken = $request->header('x-callback-token', '');
        $xenditService = app(XenditService::class);

        if (! $xenditService->verifyWebhookToken($callbackToken)) {
            Log::warning('Xendit webhook: invalid callback token', [
                'ip'    => $request->ip(),
                'token' => substr($callbackToken, 0, 10) . '...',
            ]);

            return response()->json(['status' => 'unauthorized'], 401);
        }

        // ── 2. Log payload dan dispatch ke job queue ─────────────────────
        $payload = $request->all();

        Log::info('Xendit webhook received', [
            'external_id' => $payload['external_id'] ?? 'N/A',
            'status'      => $payload['status'] ?? 'N/A',
        ]);

        ProcessXenditWebhook::dispatch($payload);

        // ── 3. Respond segera dengan HTTP 200 ────────────────────────────
        return response()->json(['status' => 'queued'], 200);
    }
}
