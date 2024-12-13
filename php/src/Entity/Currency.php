<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NewCurrencyRepository;
use App\VO\CurrencyCode;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewCurrencyRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Currency {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Наименование
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name = null;

    // Цена
    #[ORM\Column(type: Types::FLOAT)]
    private ?float $price = null;

    // Цифровой код
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $code = null;

    // Символьный код
    #[ORM\Column(type: Types::STRING, length: 7, enumType: CurrencyCode::class)]
    private ?CurrencyCode $charCode = null;

    // Дата создания
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    public function __construct(
        string $name,
        float $price,
        int $code,
        CurrencyCode $charCode,
    ) {
        $this->name = $name;
        $this->price = $price;
        $this->code = $code;
        $this->charCode = $charCode;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): static {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float {
        return $this->price;
    }

    public function setPrice(float $price): static {
        $this->price = $price;

        return $this;
    }

    public function getCode(): ?int {
        return $this->code;
    }

    public function setCode(int $code): static {
        $this->code = $code;

        return $this;
    }

    public function getCharCode(): ?CurrencyCode {
        return $this->charCode;
    }

    public function setCharCode(CurrencyCode $charCode): static {
        $this->charCode = $charCode;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }
}
