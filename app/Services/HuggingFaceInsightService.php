<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class HuggingFaceInsightService
{
    public function generateInsight(string $category, float $amount, string $month): ?array
    {
        $token = config('services.huggingface.token');
        $model = config('services.huggingface.model');

        if (!$token || !$model) {
            return null;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->post('https://api-inference.huggingface.co/models/' . $model, [
                    'inputs' => "Write one short, practical budgeting insight for a student who spent PKR " . number_format($amount, 0) . " on {$category} in {$month}. Do not invent facts or give financial guarantees.",
                    'parameters' => [
                        'max_new_tokens' => 120,
                        'return_full_text' => false,
                    ],
                ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $summary = data_get($data, '0.generated_text')
                ?? data_get($data, 'generated_text')
                ?? data_get($data, 'summary')
                ?? data_get($data, 'choices.0.message.content');

            return is_string($summary) && trim($summary) !== ''
                ? ['summary' => trim($summary)]
                : null;
        } catch (Throwable) {
            return null;
        }
    }
}