<?php

declare(strict_types=1);

namespace App\Console;

use App\DataProvider\CryptoCompare\CryptoCompareClient;
use App\Repository\CurrencyRateRepository;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class FetchCryptoCompareHistoricalDataCommand extends Command
{
    private DocumentManager $documentManager;

    private CurrencyRateRepository $currencyRateRepository;

    private CryptoCompareClient $cryptoCompareClient;

    public function __construct(
        DocumentManager $documentManager,
        CurrencyRateRepository $currencyRateRepository,
        CryptoCompareClient $cryptoCompareClient
    ) {
        parent::__construct();

        $this->documentManager = $documentManager;
        $this->currencyRateRepository = $currencyRateRepository;
        $this->cryptoCompareClient = $cryptoCompareClient;
    }

    public static function getDefaultName(): string
    {
        return 'rates:crypto-compare';
    }

    protected function configure(): void
    {
        $this->setDescription('Fetch historical rates from CryptoCompare');

        $this->addArgument('baseCurrency', InputArgument::REQUIRED);
        $this->addArgument('quotedCurrency', InputArgument::REQUIRED);
        $this->addOption('fetchToDate', null, InputOption::VALUE_REQUIRED, 'Fetch historical rate to specified date');
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit of fetched data', 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fetchToDate = $input->getOption('fetchToDate');
        if ($fetchToDate !== null) {
            $fetchToDate = new DateTimeImmutable($fetchToDate);
        }

        $fetchedRatesCollection = $this->cryptoCompareClient->getHistoricalHourlyPair(
            $input->getArgument('baseCurrency'),
            $input->getArgument('quotedCurrency'),
            (int) $input->getOption('limit'),
            $fetchToDate
        );

        $this->currencyRateRepository->clearDataByPeriod(
            $fetchedRatesCollection->getBaseCurrency(),
            $fetchedRatesCollection->getQuotedCurrency(),
            $fetchedRatesCollection->getDateFrom(),
            $fetchedRatesCollection->getDateTo()
        );

        foreach ($fetchedRatesCollection->getData() as $rate) {
            $this->documentManager->persist($rate);
        }
        $this->documentManager->flush();

        return 0;
    }
}
