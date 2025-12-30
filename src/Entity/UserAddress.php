<?php

namespace App\Entity;

use App\Repository\UserAddressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité UserAddress
 * Gère les adresses (livraison, facturation ou les deux) liées à un compte utilisateur.
 */
#[ORM\Entity(repositoryClass: UserAddressRepository::class)]
#[ORM\Table(name: 'user_addresses')]
class UserAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Relation vers l'utilisateur propriétaire de l'adresse.
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true, columnDefinition: "ENUM('shipping', 'billing', 'both')")]
    private ?string $type = 'both';

    #[ORM\Column(name: 'first_name', length: 100, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(name: 'last_name', length: 100, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(name: 'address_line1', type: Types::TEXT)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    private ?string $addressLine1 = null;

    #[ORM\Column(name: 'address_line2', type: Types::TEXT)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    private ?string $addressLine2 = null;

    #[ORM\Column(name: 'zip_code', length: 20)]
    #[Assert\NotBlank(message: "Le code postal est obligatoire.")]
    private ?string $zipCode = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "La ville est obligatoire.")]
    private ?string $city = null;

    #[ORM\Column(name: 'country', length: 45)]
    #[Assert\NotBlank]
    #[Assert\Country(message: "Le pays est obligatoire.")]
    private ?string $country;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(name: 'is_default', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $isDefault = false;

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

    // --- Getters & Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }
    public function setAddressLine1(string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;
        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }
    public function setAddressLine2(string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }
    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }
    public function setCountry(?string $countryName): self
    {
        $this->country = $countryName;
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

    public function setDeletedAt(?\DateTime $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
