<?php

namespace App\Entity;

use App\Repository\VariantOptionValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité VariantOptionValue
 * Fait le lien entre une variante de produit et une valeur d'attribut (ex: Variante #1 -> Bleu).
 */
#[ORM\Entity(repositoryClass: VariantOptionValueRepository::class)]
#[ORM\Table(name: 'variant_option_values')]
#[ORM\UniqueConstraint(name: 'unique_variant_option', columns: ['variant_id', 'attribute_value_id'])]
class VariantOptionValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * La variante de produit concernée.
     */
    #[ORM\ManyToOne(targetEntity: ProductVariant::class, inversedBy: 'variantOptionValues')]
    #[ORM\JoinColumn(name: 'variant_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?ProductVariant $variant = null;

    /**
     * La valeur d'attribut associée (ex: 'Rouge').
     */
    #[ORM\ManyToOne(targetEntity: AttributeValue::class)]
    #[ORM\JoinColumn(name: 'attribute_value_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?AttributeValue $attributeValue = null;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVariant(): ?ProductVariant
    {
        return $this->variant;
    }

    public function setVariant(?ProductVariant $variant): self
    {
        $this->variant = $variant;
        return $this;
    }

    public function getAttributeValue(): ?AttributeValue
    {
        return $this->attributeValue;
    }

    public function setAttributeValue(?AttributeValue $attributeValue): self
    {
        $this->attributeValue = $attributeValue;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}