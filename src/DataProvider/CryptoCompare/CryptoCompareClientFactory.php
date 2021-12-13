<?php

declare(strict_types=1);

namespace App\DataProvider\CryptoCompare;

use GuzzleHttp\Client;

final class CryptoCompareClientFactory
{
    public function build(
        string $apiUrl,
        string $apiKey
    ): CryptoCompareClient {
        return new CryptoCompareClient(
            new Client([
                'base_uri' => rtrim($apiUrl, '/'),
                'http_errors' => false,
            ]),
            $apiKey
        );
    }
}
