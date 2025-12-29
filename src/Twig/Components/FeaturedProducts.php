<?php

namespace App\Twig\Components;

use App\Repository\ProductRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class FeaturedProducts
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $category = null;

    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function getProducts(): array
    {
        if ($this->category) {
            // Logique de filtrage par catégorie si besoin
            return $this->productRepository->findBy(['isActive' => true], ['id' => 'DESC'], 8);
        }

        return $this->productRepository->findBy(['isActive' => true], ['id' => 'DESC'], 8);
    }
}