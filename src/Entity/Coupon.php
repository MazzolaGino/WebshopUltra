<?php

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité Coupon
 * Gère les codes promotionnels et les règles de réduction.
 */
#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ORM\Table(name: 'coupons')]
#[UniqueEntity(fields: ['code'], message: 'Ce code promotionnel existe déjà.')]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le code du coupon est obligatoire.')]
    private ?string $code = null;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('percent', 'amount')")]
    private string $type = 'percent';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive(message: 'La valeur doit être positive.')]
    private ?string $value = null;

    #[ORM\Column(name: 'min_order_amount', type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    private string $minOrderAmount = '0.00';

    #[ORM\Column(name: 'max_uses', nullable: true)]
    private ?int $maxUses = null;

    #[ORM\Column(name: 'current_uses', options: ['default' => 0])]
    private int $currentUses = 0;

    #[ORM\Column(name: 'start_date', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(name: 'end_date', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getCode(): ?string { return $this->code; }

    public function setCode(string $code): self
    {
        $this->code = strtoupper($code);
        return $this;
    }

    public function getType(): string { return $this->type; }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getValue(): ?string { return $this->value; }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getMinOrderAmount(): string { return $this->minOrderAmount; }

    public function setMinOrderAmount(string $amount): self
    {
        $this->minOrderAmount = $amount;
        return $this;
    }

    public function getMaxUses(): ?int { return $this->maxUses; }

    public function setMaxUses(?int $max): self
    {
        $this->maxUses = $max;
        return $this;
    }

    public function getCurrentUses(): int { return $this->currentUses; }

    public function setCurrentUses(int $uses): self
    {
        $this->currentUses = $uses;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface { return $this->startDate; }

    public function setStartDate(?\DateTimeInterface $date): self
    {
        $this->startDate = $date;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface { return $this->endDate; }

    public function setEndDate(?\DateTimeInterface $date): self
    {
        $this->endDate = $date;
        return $this;
    }

    public function isActive(): bool { return $this->isActive; }

    public function setIsActive(bool $active): self
    {
        $this->isActive = $active;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }

    public function getDeletedAt(): ?\DateTimeInterface { return $this->deletedAt; }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setDeletedAt(?\DateTime $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}