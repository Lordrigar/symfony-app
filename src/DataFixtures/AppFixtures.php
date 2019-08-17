<?php

namespace App\DataFixtures;

use App\Entity\Guests;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $guest = new Guests();
        $guest->setname('Lenka')->setSurname('Lika')->setNickname('Lenislava');
        $manager->persist($guest);
        $manager->flush();
    }
}
