<?php

namespace App\Service;

use App\Client\CbrClient;
use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use App\VO\CurrencyCode;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final readonly class CurrencyService {
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private EntityManagerInterface $entityManager,
        private CbrClient $client
    ) {}

    /**
     * @param DateTimeInterface $date
     * @param CurrencyCode $charCode
     * @return Currency|null
     * @throws TransportExceptionInterface
     */
    private function getCurrencyOnDate(DateTimeInterface $date, CurrencyCode $charCode): ?Currency {
        $currency = $this->currencyRepository->getByCharCodeOnDate($charCode, $date);
        if ($currency !== null) {
            return $currency;
        }

        $needed = null;
        $currencies = $this->client->getCurrencyOnDate($date);

        foreach ($currencies as $currency) {
            if ($currency->getCharCode() === $charCode) {
                $needed = $currency;
            }
            $this->entityManager->persist($currency);
        }
        $this->entityManager->flush();

        return $needed;
    }

    /**
     * @param DateTimeInterface $date
     * @param CurrencyCode $code
     * @param CurrencyCode $base
     * @return float
     * @throws TransportExceptionInterface
     */
    public function calculateCrossCurrencyOnDate(
        DateTimeInterface $date,
        CurrencyCode $code,
        CurrencyCode $base
    ): float {
        $baseCurrency = $this->getCurrencyOnDate($date, $base);
        $currency = $this->getCurrencyOnDate($date, $code);
        if ($currency === null) {
            throw new DomainException('Currency not found');
        }
        if ($baseCurrency === null) {
            throw new DomainException('Base Currency not found');
        }

        return round($baseCurrency->getPrice() / $currency->getPrice(), 4);
    }
}
