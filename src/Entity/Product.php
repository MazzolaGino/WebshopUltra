<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entité Product
 * Synchronisée avec la table 'products'. Gère le catalogue, les variantes et les images.
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?Category $category = null;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('standard','external')")]
    private string $type = 'standard';

    #[Gedmo\Translatable]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;


    #[Gedmo\Translatable]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[Gedmo\Translatable]
    #[ORM\Column(name: 'description_short', type: Types::TEXT, nullable: true)]
    private ?string $descriptionShort = null;

    #[Gedmo\Translatable]
    #[ORM\Column(name: 'description_long', type: Types::TEXT, nullable: true)]
    private ?string $descriptionLong = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\PositiveOrZero]
    private ?string $price = null;

    #[ORM\Column(name: 'sale_price', type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $salePrice = null;

    #[ORM\Column(length: 100, nullable: true, unique: true)]
    private ?string $sku = null;

    #[ORM\Column(name: 'stock_quantity', type: Types::INTEGER, options: ['default' => 0])]
    private int $stockQuantity = 0;

    #[ORM\Column(name: 'aliexpress_link', type: Types::TEXT, nullable: true)]
    private ?string $aliexpressLink = null;

    #[ORM\Column(name: 'main_image_url', length: 255, nullable: true)]
    private ?string $mainImageUrl = null;

    #[ORM\Column(name: 'is_new', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $isNew = true;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    /**
     * Relation inverse vers les variantes.
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductVariant::class, cascade: ['persist', 'remove'])]
    private Collection $variants;

    /**
     * Relation inverse vers les images secondaires.
     * Cette propriété résout l'erreur de mapping [FAIL] de l'entité ProductImage.
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductImage::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    private Collection $images;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->variants = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }

    public function getPrice(): ?string { return $this->price; }
    public function setPrice(string $price): self { $this->price = $price; return $this; }

    /** @return Collection<int, ProductVariant> */
    public function getVariants(): Collection { return $this->variants; }

    public function addVariant(ProductVariant $variant): self
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setProduct($this);
        }
        return $this;
    }

    public function removeVariant(ProductVariant $variant): self
    {
        if ($this->variants->removeElement($variant)) {
            if ($variant->getProduct() === $this) {
                $variant->setProduct(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, ProductImage> */
    public function getImages(): Collection { return $this->images; }

    public function addImage(ProductImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }
        return $this;
    }

    public function removeImage(ProductImage $image): self
    {
        if ($this->images->removeElement($image)) {
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescriptionShort(): ?string
    {
        return $this->descriptionShort;
    }

    public function setDescriptionShort(?string $descriptionShort): static
    {
        $this->descriptionShort = $descriptionShort;

        return $this;
    }

    public function getDescriptionLong(): ?string
    {
        return $this->descriptionLong;
    }

    public function setDescriptionLong(?string $descriptionLong): static
    {
        $this->descriptionLong = $descriptionLong;

        return $this;
    }

    public function getSalePrice(): ?string
    {
        return $this->salePrice;
    }

    public function setSalePrice(?string $salePrice): static
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getStockQuantity(): ?int
    {
        return $this->stockQuantity;
    }

    public function setStockQuantity(int $stockQuantity): static
    {
        $this->stockQuantity = $stockQuantity;

        return $this;
    }

    public function getAliexpressLink(): ?string
    {
        return $this->aliexpressLink;
    }

    public function setAliexpressLink(?string $aliexpressLink): static
    {
        $this->aliexpressLink = $aliexpressLink;

        return $this;
    }

    public function getMainImageUrl(): ?string
    {
        return $this->mainImageUrl;
    }

    public function setMainImageUrl(?string $mainImageUrl): static
    {
        $this->mainImageUrl = $mainImageUrl;

        return $this;
    }

    public function isNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): static
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

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

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}