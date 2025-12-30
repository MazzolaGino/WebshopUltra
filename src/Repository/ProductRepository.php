<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function findActiveProducts(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche textuelle dans le nom et les descriptions du produit [3]
     */
    public function searchProducts(string $searchTerm, int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :searchTerm OR p.descriptionShort LIKE :searchTerm OR p.descriptionLong LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Filtre les produits par catégorie [4]
     */
    public function findProductsByCategory(string $categorySlug, int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $categorySlug)
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche avancée incluant prix, stock et nouveautés [5, 6]
     */
    public function findByAdvancedFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true);

        if (!empty($filters['minPrice'])) {
            $qb->andWhere('p.price >= :minPrice')->setParameter('minPrice', $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $qb->andWhere('p.price <= :maxPrice')->setParameter('maxPrice', $filters['maxPrice']);
        }

        if (isset($filters['inStock']) && $filters['inStock'] === true) {
            $qb->andWhere('p.stockQuantity > 0'); // [6]
        }

        if (isset($filters['isNew']) && $filters['isNew'] === true) {
            $qb->andWhere('p.isNew = :isNew')->setParameter('isNew', true); // [7]
        }

        return $qb->orderBy('p.createdAt', 'DESC')->getQuery()->getResult();
    }
}