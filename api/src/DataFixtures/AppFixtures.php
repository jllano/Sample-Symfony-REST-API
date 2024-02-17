<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product1 = new Product();
        $product1->setName('iPhone');
        $product1->setPrice(mt_rand(10, 100));
        $manager->persist($product1);
        $this->addReference('iPhone', $product1);

        $product2 = new Product();
        $product2->setName('Headphones');
        $product2->setPrice(mt_rand(10, 100));
        $manager->persist($product2);
        $this->addReference('headPhones', $product2);

        $product3 = new Product();
        $product3->setName('Case');
        $product3->setPrice(mt_rand(10, 100));
        $manager->persist($product3);
        $this->addReference('case', $product3);

        $manager->flush();
    }
}
