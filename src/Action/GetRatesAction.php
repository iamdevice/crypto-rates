<?php

declare(strict_types=1);

namespace App\Action;

use App\Repository\CurrencyRateRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetRatesAction
{
    private const HOURLY_PERIOD = 'hourly';

    private const DAILY_PERIOD = 'daily';

    private CurrencyRateRepository $rateRepository;

    public function __construct(
        CurrencyRateRepository $currencyRateRepository
    ) {
        $this->rateRepository = $currencyRateRepository;
    }

    public function __invoke(
        string $baseCurrency,
        string $quotedCurrency,
        Request $request
    ): Response {
        $period = $request->query->get('period') ?? self::HOURLY_PERIOD;

        switch ($period) {
            case self::DAILY_PERIOD:
                $rates = $this->rateRepository->getDailyCollectionByCurrencyPair($baseCurrency, $quotedCurrency);

                break;
            case self::HOURLY_PERIOD:
            default:
                $rates = $this->rateRepository->getCollectionByCurrencyPair($baseCurrency, $quotedCurrency);
        }

        return new JsonResponse($rates);
    }
}
