<?php

namespace App\Command;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsCommand(
    name: 'app:seed:products',
    description: 'Insère 10 produits premium en base de données pour les tests.',
)]
class SeedProductsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $slugger = new AsciiSlugger();
        
        $io->title('Initialisation du catalogue de test');

        // 1. Gestion de la Catégorie
        $categoryRepo = $this->entityManager->getRepository(Category::class);
        // On cherche une catégorie existante pour éviter de recréer un slug identique
        $category = $categoryRepo->findOneBy(['name' => 'High-Tech']) ?? $categoryRepo->findOneBy([]) ?? new Category();
        
        if (!$category->getId()) {
            $category->setName('High-Tech');
            // On s'assure que le slug de la catégorie n'est pas nul si le champ existe
            if (method_exists($category, 'setSlug')) {
                $category->setSlug('high-tech');
                $category->setCreatedAt(new \DateTime());
                $category->setUpdatedAt(new \DateTime());
            }
            $this->entityManager->persist($category);
            $io->note('Création d\'une nouvelle catégorie : High-Tech');
        }

        // 2. Définition des 10 produits
        $productsData = [
            ['Ultra Drone 4K', 'Drone professionnel avec caméra stabilisée.', '799.00', 'SKU-DRN-001'],
            ['Station de Charge Solaire', 'Chargez vos appareils partout grâce au soleil.', '49.90', 'SKU-SOL-042'],
            ['Clavier Mécanique Silent', 'Le confort du mécanique sans le bruit.', '120.00', 'SKU-KBD-99'],
            ['Ecouteurs Bio-Hacker', 'Isolation phonique active et design organique.', '159.00', 'SKU-EAR-05'],
            ['Lampe Holographique', 'Une ambiance futuriste pour votre bureau.', '89.00', 'SKU-LMP-12'],
            ['Souris Verticale Pro', 'Ergonomie avancée pour éviter les TMS.', '65.00', 'SKU-MSE-08'],
            ['Sac à Dos Antivol', 'Protection RFID et fermetures cachées.', '75.00', 'SKU-BAG-21'],
            ['Purificateur d\'Air IoT', 'Contrôlez la qualité de votre air via app.', '210.00', 'SKU-AIR-03'],
            ['Support Laptop Alu', 'Refroidissement passif et design minimaliste.', '35.00', 'SKU-STN-07'],
            ['Câble USB-C Indestructible', 'Tressage en nylon militaire, garanti à vie.', '19.90', 'SKU-CBL-01'],
        ];

        foreach ($productsData as $data) {
            // Vérification anti-doublon pour le SKU (unique)
            $existing = $this->entityManager->getRepository(Product::class)->findOneBy(['sku' => $data[3]]);
            if ($existing) {
                continue;
            }

            $product = new Product();
            
            // Forcer la locale si vous utilisez le TranslatableTrait de Gedmo
            if (method_exists($product, 'setTranslatableLocale')) {
                $product->setTranslatableLocale('fr'); 
            }

            $product->setName($data[0]);
            
            // Génération du slug
            $generatedSlug = strtolower((string)$slugger->slug($data[0]));
            $product->setSlug($generatedSlug);
            
            $product->setDescriptionShort($data[1]);
            $product->setDescriptionLong($data[1] . " Produit premium sélectionné pour sa durabilité.");
            $product->setPrice($data[2]);
            $product->setSalePrice(number_format((float)$data[2] * 0.85, 2, '.', '')); 
            $product->setSku($data[3]);
            $product->setStockQuantity(rand(10, 100));
            $product->setType('standard');
            $product->setIsNew(true);
            $product->setIsActive(true);
            $product->setCategory($category);
            $product->setMainImageUrl('https://picsum.photos/seed/' . md5($data[0]) . '/600/600');
            
            // Dates
            $product->setCreatedAt(new \DateTime());
            $product->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($product);
        }

        try {
            $this->entityManager->flush();
            $io->success('Succès : Les produits et la catégorie ont été insérés.');
        } catch (\Exception $e) {
            $io->error('Erreur SQL : ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}