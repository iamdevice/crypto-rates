<?php

declare(strict_types=1);

namespace App\Document;

use DateTimeImmutable;
use JsonSerializable;
use MongoDB\BSON\Decimal128;

class CurrencyRate implements JsonSerializable
{
    private string $id;

    private string $baseCurrency;

    private string $quotedCurrency;

    private DateTimeImmutable $date;

    private string $openRate;

    private string $closeRate;

    private string $highRate;

    private string $lowRate;

    public function __construct(
        string $baseCurrency,
        string $quotedCurrency,
        DateTimeImmutable $date,
        Decimal128 $openRate,
        Decimal128 $closeRate,
        Decimal128 $highRate,
        Decimal128 $lowRate
    ) {
        $this->baseCurrency = $baseCurrency;
        $this->quotedCurrency = $quotedCurrency;
        $this->date = $date->setTime((int) $date->format('H'), 0, 0);
        $this->openRate = (string) $openRate;
        $this->closeRate = (string) $closeRate;
        $this->highRate = (string) $highRate;
        $this->lowRate = (string) $lowRate;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function getQuotedCurrency(): string
    {
        return $this->quotedCurrency;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getOpenRate(): string
    {
        return $this->openRate;
    }

    public function getCloseRate(): string
    {
        return $this->closeRate;
    }

    public function getHighRate(): string
    {
        return $this->highRate;
    }

    public function getLowRate(): string
    {
        return $this->lowRate;
    }

    public function jsonSerialize(): array
    {
        return [
            'base' => $this->getBaseCurrency(),
            'quoted' => $this->getQuotedCurrency(),
            'date' => $this->getDate()->format('Y-m-d H:i:s'),
            'openRate' => $this->getOpenRate(),
            'closeRate' => $this->getCloseRate(),
            'highRate' => $this->getHighRate(),
            'lowRate' => $this->getLowRate(),
        ];
    }
}
