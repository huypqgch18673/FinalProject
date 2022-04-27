<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; $i++) {
            $brand = new Brand();
            $brand->setName("Brand $i");
            $brand->setOwner("Mr. $i");
            $brand->setValue(30);
            $brand->setImage("https://vtv1.mediacdn.vn/thumb_w/650/2019/2/12/gucci-logo1-1549930582778456574934.jpg");
            $brand->setRate(5);
            $manager->persist($brand);
        }

        $manager->flush();
    }
}
