<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture; // Correction ici
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $countries = [
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'Belgique', 'code' => 'BE'],
            ['name' => 'Suisse', 'code' => 'CH'],
            ['name' => 'Luxembourg', 'code' => 'LU'],
            ['name' => 'Italie', 'code' => 'IT'],
            ['name' => 'Espagne', 'code' => 'ES'],
            ['name' => 'Allemagne', 'code' => 'DE'],
        ];

        foreach ($countries as $data) {
            $country = new Country();
            $country->setName($data['name']);
            $country->setIsoCode($data['code']);
            $country->setActive(true);
            $manager->persist($country);
        }

        $manager->flush();
    }
}