<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

final class CurrencyRateRepository extends DocumentRepository
{
    public function getCollectionByCurrencyPair(
        string $baseCurrency,
        string $quotedCurrency,
        int $limit = 100
    ): array {
        return $this->findBy(
            ['baseCurrency' => $baseCurrency, 'quotedCurrency' => $quotedCurrency],
            ['date' => 'asc'],
            $limit
        );
    }

    public function getDailyCollectionByCurrencyPair(
        string $baseCurrency,
        string $quotedCurrency,
        int $limit = 100
    ): array {
        $builder = $this->createAggregationBuilder();
        $builder
            ->match()
                ->field('baseCurrency')->equals($baseCurrency)
                ->field('quotedCurrency')->equals($quotedCurrency)
            ->addFields()
                ->field('day')->dateToString('%Y-%m-%d 00:00:00', '$date')
            ->sort('day', 'asc')
            ->sort('date', 'asc')
            ->group()
                ->field('id')->expression(
                    $builder
                        ->expr()
                        ->field('baseCurrency')->expression('$baseCurrency')
                        ->field('quotedCurrency')->expression('$quotedCurrency')
                        ->field('date')->expression('$day')
                )
                ->field('openRate')->first('$openRate')
                ->field('closeRate')->last('$closeRate')
                ->field('lowRate')->min('$lowRate')
                ->field('highRate')->max('$highRate')
            ->sort('_id.date', 'asc')
            ->limit($limit);

        $result = [];
        foreach ($builder->getAggregation()->getIterator() as $item) {
            $result[] = [
                'base' => $item['_id']['baseCurrency'],
                'quoted' => $item['_id']['quotedCurrency'],
                'date' => $item['_id']['date'],
                'openRate' => (string) $item['openRate'],
                'closeRate' => (string) $item['closeRate'],
                'highRate' => (string) $item['highRate'],
                'lowRate' => (string) $item['lowRate'],
            ];
        }

        return $result;
    }

    public function clearDataByPeriod(
        string $baseCurrency,
        string $quotedCurrency,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): void {
        $this->createQueryBuilder()
            ->remove()
            ->field('baseCurrency')->equals($baseCurrency)
            ->field('quotedCurrency')->equals($quotedCurrency)
            ->field('date')->range($from, $to)
            ->getQuery()
            ->execute();
    }
}
