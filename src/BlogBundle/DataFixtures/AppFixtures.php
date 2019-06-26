<?php
namespace BlogBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use BlogBundle\Entity\Post;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load( ObjectManager $manager)
    {
        $faker = Factory::create();

        for($i=0; $i<200; $i++){
            $post = new Post();
            $post->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
            $post->setBody($faker->paragraph($nbSentences = 2, $variableNbSentences = true));
            $post->setTag("Ciencia");
            $post->setCreateAt(new \DateTime('now'));
            $manager->persist($post);
            $manager->flush();
        }
    }
}