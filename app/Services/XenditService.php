<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service untuk integrasi Xendit Payment Gateway.
 *
 * Menggunakan Xendit Invoice API v2 untuk membuat invoice pembayaran
 * dan memverifikasi webhook callback token.
 *
 * @see https://developers.xendit.co/api-reference/#invoices
 */
class XenditService
{
    private string $secretKey;
    private string $webhookToken;
    private string $baseUrl;

    public function __construct()
    {
        $this->secretKey    = config('services.xendit.secret_key');
        $this->webhookToken = config('services.xendit.webhook_token');
        $this->baseUrl      = config('services.xendit.base_url', 'https://api.xendit.co');
    }

    /**
     * Buat invoice pembayaran baru di Xendit.
     *
     * @param  array  $params  [
     *     'order_number'   => string,  // ID pesanan lokal
     *     'amount'         => int,     // Jumlah dalam Rupiah
     *     'payer_email'    => string,  // Email customer
     *     'description'    => string,  // Deskripsi pembayaran
     *     'customer_name'  => string,  // Nama customer (opsional)
     *     'items'          => array,   // Detail item (opsional)
     * ]
     * @return array{
     *     success: bool,
     *     invoice_id: string|null,
     *     invoice_url: string|null,
     *     external_id: string|null,
     *     error: string|null
     * }
     */
    public function createInvoice(array $params): array
    {
        $externalId = 'INV-' . $params['order_number'] . '-' . Str::random(6);

        $payload = [
            'external_id'      => $externalId,
            'amount'           => $params['amount'],
            'payer_email'      => $params['payer_email'],
            'description'      => $params['description'] ?? "Pembayaran pesanan {$params['order_number']}",
            'currency'         => 'IDR',
            'invoice_duration' => 86400, // 24 jam
            'success_redirect_url' => url('/order/success/' . rtrim(strtr(base64_encode($params['order_number']), '+/', '-_'), '=')),
            'failure_redirect_url' => url("/cart"),
        ];

        // Tambahkan customer info jika tersedia
        if (! empty($params['customer_name'])) {
            $payload['customer'] = [
                'given_names' => $params['customer_name'],
                'email'       => $params['payer_email'],
            ];
        }

        // Tambahkan detail items jika tersedia
        if (! empty($params['items'])) {
            $payload['items'] = collect($params['items'])->map(fn($item) => [
                'name'     => $item['name'],
                'quantity' => $item['quantity'],
                'price'    => $item['price'],
            ])->toArray();
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->timeout(30)
                ->retry(2, 500)
                ->post("{$this->baseUrl}/v2/invoices", $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Xendit invoice created', [
                    'external_id' => $externalId,
                    'invoice_id'  => $data['id'],
                ]);

                return [
                    'success'     => true,
                    'invoice_id'  => $data['id'],
                    'invoice_url' => $data['invoice_url'],
                    'external_id' => $externalId,
                    'error'       => null,
                ];
            }

            Log::error('Xendit invoice creation failed', [
                'status'  => $response->status(),
                'body'    => $response->body(),
                'payload' => $payload,
            ]);

            return [
                'success'     => false,
                'invoice_id'  => null,
                'invoice_url' => null,
                'external_id' => $externalId,
                'error'       => "Xendit error [{$response->status()}]: {$response->body()}",
            ];

        } catch (\Exception $e) {
            Log::error('Xendit API exception', [
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return [
                'success'     => false,
                'invoice_id'  => null,
                'invoice_url' => null,
                'external_id' => $externalId,
                'error'       => 'Gagal menghubungi Xendit: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verifikasi token webhook yang dikirim Xendit pada header
     * `x-callback-token` untuk memastikan request benar dari Xendit.
     *
     * @param  string  $callbackToken  Nilai header x-callback-token dari request
     * @return bool
     */
    public function verifyWebhookToken(string $callbackToken): bool
    {
        if (empty($this->webhookToken)) {
            Log::warning('Xendit webhook token not configured');
            return false;
        }

        return hash_equals($this->webhookToken, $callbackToken);
    }

    /**
     * Ambil detail invoice dari Xendit berdasarkan invoice ID.
     *
     * @param  string  $invoiceId  ID invoice Xendit
     * @return array|null
     */
    public function getInvoice(string $invoiceId): ?array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->timeout(15)
                ->get("{$this->baseUrl}/v2/invoices/{$invoiceId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit get invoice failed', [
                'invoice_id' => $invoiceId,
                'status'     => $response->status(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Xendit get invoice exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
