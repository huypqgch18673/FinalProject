<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $country = new Country();
            $country->setName("Country $i");
            $country->setImage("https://cdn.britannica.com/41/4041-004-D051B135/Flag-Vietnam.jpg");
            $manager->persist($country);
        }

        $manager->flush();
    }
}
