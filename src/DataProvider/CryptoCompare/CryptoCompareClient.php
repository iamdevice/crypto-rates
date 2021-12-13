<?php

declare(strict_types=1);

namespace App\DataProvider\CryptoCompare;

use App\DataProvider\DataRetrievalFailedException;
use DateTimeImmutable;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Response;

final class CryptoCompareClient
{
    private ClientInterface $client;

    private string $apiKey;

    public function __construct(
        ClientInterface $client,
        string $apiKey
    ) {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function getHistoricalHourlyPair(
        string $baseCurrency,
        string $quotedCurrency,
        int $limit = 100,
        ?DateTimeImmutable $retrieveToDate = null
    ): CryptoCompareHistoricalRatesCollection {
        $queryParameters = ['fsym' => $baseCurrency, 'tsym' => $quotedCurrency, 'limit' => $limit];
        if ($retrieveToDate instanceof DateTimeImmutable) {
            $queryParameters['toTs'] = $retrieveToDate->getTimestamp();
        }

        $response = $this->client->sendRequest(
            new Request(
                'GET',
                '/data/v2/histohour?' . http_build_query($queryParameters),
                [
                    'CRYPTOCOMPARE_API_KEY' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
            )
        );

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new DataRetrievalFailedException('CryptoCompare');
        }

        $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return new CryptoCompareHistoricalRatesCollection(
            $baseCurrency,
            $quotedCurrency,
            $responsePayload
        );
    }
}
