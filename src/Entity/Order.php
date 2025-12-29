<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité Order
 * Gère les transactions finales. Utilise des snapshots JSON pour les adresses.
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Relation vers l'utilisateur (peut être null si compte supprimé)
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(name: 'order_number', length: 20, unique: true)]
    #[Assert\NotBlank]
    private ?string $orderNumber = null;

    #[ORM\Column(name: 'total_ht', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalHt = null;

    #[ORM\Column(name: 'total_tax', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalTax = null;

    #[ORM\Column(name: 'total_shipping', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalShipping = null;

    #[ORM\Column(name: 'total_ttc', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalTtc = null;

    #[ORM\Column(type: 'string', length: 20, columnDefinition: "ENUM('pending','paid','processing','shipped','delivered','cancelled')")]
    private string $status = 'pending';

    /**
     * Snapshot de l'adresse de livraison au moment de la commande
     */
    #[ORM\Column(name: 'shipping_address_snapshot', type: Types::JSON)]
    private array $shippingAddressSnapshot = [];

    /**
     * Snapshot de l'adresse de facturation au moment de la commande
     */
    #[ORM\Column(name: 'billing_address_snapshot', type: Types::JSON)]
    private array $billingAddressSnapshot = [];

    #[ORM\Column(name: 'tracking_number', length: 100, nullable: true)]
    private ?string $trackingNumber = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    /**
     * Articles liés à cette commande
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    private Collection $orderItems;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->orderItems = new ArrayCollection();
    }

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    public function getOrderNumber(): ?string { return $this->orderNumber; }
    public function setOrderNumber(string $num): self { $this->orderNumber = $num; return $this; }

    public function getTotalTtc(): ?string { return $this->totalTtc; }
    public function setTotalTtc(string $total): self { $this->totalTtc = $total; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getShippingAddressSnapshot(): array { return $this->shippingAddressSnapshot; }
    public function setShippingAddressSnapshot(array $data): self { $this->shippingAddressSnapshot = $data; return $this; }

    /** @return Collection<int, OrderItem> */
    public function getOrderItems(): Collection { return $this->orderItems; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }

    public function getTotalHt(): ?string
    {
        return $this->totalHt;
    }

    public function setTotalHt(string $totalHt): static
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    public function getTotalTax(): ?string
    {
        return $this->totalTax;
    }

    public function setTotalTax(string $totalTax): static
    {
        $this->totalTax = $totalTax;

        return $this;
    }

    public function getTotalShipping(): ?string
    {
        return $this->totalShipping;
    }

    public function setTotalShipping(string $totalShipping): static
    {
        $this->totalShipping = $totalShipping;

        return $this;
    }

    public function getBillingAddressSnapshot(): array
    {
        return $this->billingAddressSnapshot;
    }

    public function setBillingAddressSnapshot(array $billingAddressSnapshot): static
    {
        $this->billingAddressSnapshot = $billingAddressSnapshot;

        return $this;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber(?string $trackingNumber): static
    {
        $this->trackingNumber = $trackingNumber;

        return $this;
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

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }
}