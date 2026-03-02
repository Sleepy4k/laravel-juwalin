<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Integration with Pakasir payment link service.
 *
 * @see https://pakasir.com/p/docs
 */
class PakasirService
{
    private const BASE_URL = 'https://app.pakasir.com';

    private readonly string $project;

    private readonly string $apiKey;

    private readonly bool $sandbox;

    public function __construct()
    {
        $this->project = (string) config('pakasir.project');
        $this->apiKey = (string) config('pakasir.api_key');
        $this->sandbox = (bool) config('pakasir.sandbox', true);
    }

    /**
     * Generate the Pakasir payment URL (integration via URL).
     * Redirect customer to this URL to make payment.
     */
    public function paymentUrl(string $orderId, int $amount, string $redirectUrl = ''): string
    {
        $url = self::BASE_URL . "/pay/{$this->project}/{$amount}?order_id={$orderId}";

        if ($redirectUrl !== '') {
            $url .= '&redirect=' . urlencode($redirectUrl);
        }

        return $url;
    }

    /**
     * Create a transaction via API (returns QRIS string or VA number).
     *
     * @param  string               $method one of: qris, bri_va, bni_va, bca_va, etc
     * @return array<string, mixed>
     *
     * @throws RuntimeException
     */
    public function createTransaction(string $orderId, int $amount, string $method = 'qris'): array
    {
        $response = Http::post(
            self::BASE_URL . "/api/transactioncreate/{$method}",
            [
                'project'  => $this->project,
                'order_id' => $orderId,
                'amount'   => $amount,
                'api_key'  => $this->apiKey,
            ],
        );

        if ($response->failed()) {
            throw new RuntimeException('Pakasir API error: ' . $response->body());
        }

        return $response->json('payment') ?? [];
    }

    /**
     * Get transaction detail / status.
     *
     * @return array<string, mixed>
     */
    public function getTransaction(string $orderId, int $amount): array
    {
        $response = Http::get(self::BASE_URL . '/api/transactiondetail', [
            'project'  => $this->project,
            'order_id' => $orderId,
            'amount'   => $amount,
            'api_key'  => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Pakasir API error: ' . $response->body());
        }

        return $response->json('transaction') ?? [];
    }

    /**
     * Cancel a transaction.
     */
    public function cancelTransaction(string $orderId, int $amount): bool
    {
        $response = Http::post(self::BASE_URL . '/api/transactioncancel', [
            'project'  => $this->project,
            'order_id' => $orderId,
            'amount'   => $amount,
            'api_key'  => $this->apiKey,
        ]);

        return $response->successful();
    }

    /**
     * Simulate payment (sandbox only).
     */
    public function simulatePayment(string $orderId, int $amount): bool
    {
        if (!$this->sandbox) {
            return false;
        }

        try {
            $response = Http::post(self::BASE_URL . '/api/paymentsimulation', [
                'project'  => $this->project,
                'order_id' => $orderId,
                'amount'   => $amount,
                'api_key'  => $this->apiKey,
            ]);

            return $response->successful();
        } catch (RequestException) {
            return false;
        }
    }

    /**
     * Verify a webhook payload is legitimate.
     * Pakasir sends: amount, order_id, project, status, payment_method, completed_at.
     *
     * @param array<string, mixed> $payload
     */
    public function verifyWebhook(array $payload): bool
    {
        return isset($payload['status'], $payload['order_id'], $payload['project'])
            && $payload['project'] === $this->project
            && $payload['status'] === 'completed';
    }
}
