<?php

namespace App\Entity;

use App\Repository\ProductVariantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité ProductVariant
 * Représente une déclinaison spécifique d'un produit (ex: Taille L, Couleur Rouge).
 */
#[ORM\Entity(repositoryClass: ProductVariantRepository::class)]
#[ORM\Table(name: 'product_variants')]
class ProductVariant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Relation vers le produit parent
     */
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'variants')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: "Le produit parent est obligatoire.")]
    private ?Product $product = null;

    #[ORM\Column(length: 100, unique: true, nullable: true)]
    #[Assert\Length(max: 100)]
    private ?string $sku = null;

    #[ORM\Column(name: 'price_modifier', type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Assert\Type(type: 'numeric')]
    private string $priceModifier = '0.00';

    #[ORM\Column(name: 'stock_quantity', type: Types::INTEGER, options: ['default' => 0])]
    #[Assert\PositiveOrZero(message: "Le stock ne peut pas être négatif.")]
    private int $stockQuantity = 0;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    /**
     * Relation vers les options de la variante (pivot variant_option_values)
     */
    #[ORM\OneToMany(mappedBy: 'variant', targetEntity: VariantOptionValue::class, cascade: ['persist', 'remove'])]
    private Collection $variantOptionValues;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->variantOptionValues = new ArrayCollection();
    }

    // --- Getters et Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }

    public function getPriceModifier(): string
    {
        return $this->priceModifier;
    }

    public function setPriceModifier(string $priceModifier): self
    {
        $this->priceModifier = $priceModifier;
        return $this;
    }

    public function getStockQuantity(): int
    {
        return $this->stockQuantity;
    }

    public function setStockQuantity(int $stockQuantity): self
    {
        $this->stockQuantity = $stockQuantity;
        return $this;
    }

    /** @return Collection<int, VariantOptionValue> */
    public function getVariantOptionValues(): Collection
    {
        return $this->variantOptionValues;
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

    public function addVariantOptionValue(VariantOptionValue $variantOptionValue): static
    {
        if (!$this->variantOptionValues->contains($variantOptionValue)) {
            $this->variantOptionValues->add($variantOptionValue);
            $variantOptionValue->setVariant($this);
        }

        return $this;
    }

    public function removeVariantOptionValue(VariantOptionValue $variantOptionValue): static
    {
        if ($this->variantOptionValues->removeElement($variantOptionValue)) {
            // set the owning side to null (unless already changed)
            if ($variantOptionValue->getVariant() === $this) {
                $variantOptionValue->setVariant(null);
            }
        }

        return $this;
    }
}