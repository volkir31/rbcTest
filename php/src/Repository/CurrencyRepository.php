<?php

namespace App\Repository;

use App\Entity\Currency;
use App\VO\CurrencyCode;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Currency>
 */
class CurrencyRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Currency::class);
    }

    /**
     * Получить курс по символьному коду
     *
     * @param CurrencyCode $charCode
     * @param DateTimeInterface $date
     * @return Currency|null
     */
    public function getByCharCodeOnDate(CurrencyCode $charCode, DateTimeInterface $date): ?Currency {
        if ($charCode->isRur()) {
            return new Currency('Российский рубль', 1.0, 0, $charCode);
        }
        return $this->findOneBy(['charCode' => $charCode, 'createdAt' => $date]);
    }
}
