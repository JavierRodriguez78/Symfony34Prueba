<?php
namespace BlogBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use BlogBundle\Entity\User;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load( ObjectManager $manager)
    {
        $faker = Factory::create();

        for($i=0; $i<10; $i++){
            $user = new User();
            $user->setName($faker->firstNameFemale());
            $user->setMail($faker->unique()->email);
            $manager->persist($user);
            $manager->flush();
        }
    }
}