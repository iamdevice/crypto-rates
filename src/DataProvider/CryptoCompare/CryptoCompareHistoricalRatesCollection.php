<?php

declare(strict_types=1);

namespace App\DataProvider\CryptoCompare;

use App\Document\CurrencyRate;
use DateTimeImmutable;
use MongoDB\BSON\Decimal128;

final class CryptoCompareHistoricalRatesCollection
{
    private DateTimeImmutable $dateFrom;

    private DateTimeImmutable $dateTo;

    private string $baseCurrency;

    private string $quotedCurrency;

    private array $data;

    public function __construct(
        string $baseCurrency,
        string $quotedCurrency,
        array $data
    ) {
        $this->dateFrom = (new DateTimeImmutable())->setTimestamp((int) ($data['Data']['TimeFrom'] ?? time()));
        $this->dateTo   = (new DateTimeImmutable())->setTimestamp((int) ($data['Data']['TimeTo'] ?? time()));
        $this->baseCurrency = $baseCurrency;
        $this->quotedCurrency = $quotedCurrency;

        foreach (($data['Data']['Data'] ?? []) as $item) {
            $this->data[] = new CurrencyRate(
                $baseCurrency,
                $quotedCurrency,
                (new DateTimeImmutable())->setTimestamp((int) $item['time']),
                new Decimal128((string) $item['open']),
                new Decimal128((string) $item['close']),
                new Decimal128((string) $item['high']),
                new Decimal128((string) $item['low'])
            );
        }
    }

    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    public function getDateTo()
    {
        return $this->dateTo;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function getQuotedCurrency(): string
    {
        return $this->quotedCurrency;
    }

    /**
     * @return array<CurrencyRate>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
