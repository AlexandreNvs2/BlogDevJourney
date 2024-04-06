<?php

namespace App\DataFixtures;

use App\Entity\Post\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager) : void
    {

        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->words(1, true) . ' ' . $i)
                ->setDescription(
                    mt_rand(0, 1) === 1 ? $faker->realText(250) : null
                );

            $manager->persist($category);

        }
        $manager->flush();
    }

}