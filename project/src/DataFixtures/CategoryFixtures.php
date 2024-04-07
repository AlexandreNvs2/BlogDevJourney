<?php

namespace App\DataFixtures;

use App\Entity\Post\Category;
use App\Repository\Post\PostRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private PostRepository $postRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categoryNames = [
            'Développement Front-End',
            'Développement Back-End',
            'DevOps',
            'Conception UI/UX',
            'Sécurité informatique',
            'Data Science',
            'Intelligence Artificielle',
            'Développement mobile',
            'Gestion de projet informatique',
            'Tests et qualité logicielle'
        ];

        $categories = [];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name)
                ->setDescription($faker->realText(254));

            $manager->persist($category);
            $categories[] = $category;
        }

        // Associer les posts à des catégories aléatoires
        $posts = $this->postRepository->findAll();
        foreach ($posts as $post) {
            for ($i = 0; $i < mt_rand(1, 3); $i++) { // Supposons chaque post peut appartenir à 1 à 3 catégories
                $post->addCategory($categories[mt_rand(0, count($categories) - 1)]);
            }
        }

        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [PostFixtures::class];
    }
}
