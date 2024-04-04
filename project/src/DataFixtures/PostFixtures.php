<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post\Post;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 150; $i++) {
            $post = new Post();
            $title = $faker->unique()->word . ' ' . $faker->words(3, true); // Assure un meilleur niveau d'unicité
            $post->setTitle($title)
                ->setContent($faker->realText(1800))
                ->setState(mt_rand(0, 2) === 1 ? Post::STATES[0] : Post::STATES[1]);

            $manager->persist($post);
        }
    }
    }
