<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Coupon;
use App\Enum\CouponType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadProduct($manager);
        $this->loadCoupon($manager);
        $manager->flush();
    }

    private function loadProduct(ObjectManager $manager)
    {
        $product1 = new Product();
        $product1->setName('iPhone');
        $product1->setPrice(80.50);
        $manager->persist($product1);
        $this->addReference('iPhone', $product1);

        $product2 = new Product();
        $product2->setName('Headphones');
        $product2->setPrice(50.23);
        $manager->persist($product2);
        $this->addReference('headPhones', $product2);

        $product3 = new Product();
        $product3->setName('Case');
        $product3->setPrice(23.00);
        $manager->persist($product3);
        $this->addReference('case', $product3);
    }

    private function loadCoupon(ObjectManager $manager)
    {
        $coupon1 = new Coupon();
        $coupon1->setCode('FC5');
        $coupon1->setType(CouponType::FIXED);
        $coupon1->setAmount(5);
        $manager->persist($coupon1);
        $this->addReference('fixedCoupon', $coupon1);

        $coupon2 = new Coupon();
        $coupon2->setCode('PC30');
        $coupon2->setType(CouponType::PERCENTAGE);
        $coupon2->setAmount(30);
        $manager->persist($coupon2);
        $this->addReference('percentageCoupon', $coupon2);
    }
}
