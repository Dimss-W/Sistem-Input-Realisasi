<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    /**
     * Cache key for exchange rates.
     */
    protected const CACHE_KEY = 'live_exchange_rates_idr';

    /**
     * Cache duration in seconds (1 hour = 3600 seconds).
     */
    protected const CACHE_TTL = 3600;

    /**
     * Fallback rates if API or internet connection is unavailable.
     */
    protected array $fallbackRates = [
        'USD' => 17018.425,
        'EUR' => 18500.00,
        'SGD' => 12800.00,
        'JPY' => 110.00,
        'IDR' => 1.0,
    ];

    /**
     * Get live exchange rates for supported currencies to IDR.
     *
     * @param bool $forceRefresh
     * @return array
     */
    public function getLiveRates(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->fetchFromApi();
        });
    }

    /**
     * Fetch live rates from external public FX API.
     *
     * @return array
     */
    protected function fetchFromApi(): array
    {
        try {
            // Primary API: open.er-api.com (No API key needed, real-time rates)
            $response = Http::timeout(5)->get('https://open.er-api.com/v6/latest/USD');

            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'] ?? [];

                if (isset($rates['IDR']) && $rates['IDR'] > 0) {
                    $usdToIdr = (float) $rates['IDR'];
                    $eurRate  = isset($rates['EUR']) && $rates['EUR'] > 0 ? round($usdToIdr / $rates['EUR'], 2) : 18500.0;
                    $sgdRate  = isset($rates['SGD']) && $rates['SGD'] > 0 ? round($usdToIdr / $rates['SGD'], 2) : 12800.0;
                    $jpyRate  = isset($rates['JPY']) && $rates['JPY'] > 0 ? round($usdToIdr / $rates['JPY'], 2) : 110.0;

                    $lastUpdate = isset($data['time_last_update_unix'])
                        ? date('d M Y H:i', $data['time_last_update_unix']) . ' WIB'
                        : date('d M Y H:i') . ' WIB';

                    return [
                        'rates' => [
                            'IDR' => 1.0,
                            'USD' => round($usdToIdr, 2),
                            'EUR' => $eurRate,
                            'SGD' => $sgdRate,
                            'JPY' => $jpyRate,
                        ],
                        'last_updated' => $lastUpdate,
                        'source'       => 'Live Internet FX Market (ER-API)',
                        'is_live'      => true,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ExchangeRateService API fetch failed: ' . $e->getMessage());
        }

        // Return fallback rates if API call fails
        return [
            'rates'        => $this->fallbackRates,
            'last_updated' => date('d M Y H:i') . ' WIB',
            'source'       => 'Baseline Exchange Rates (Offline Mode)',
            'is_live'      => false,
        ];
    }
}
