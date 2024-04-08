<?php

namespace App\Tests\Functionnal\Category;

use App\Entity\Post\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Post\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryTest extends WebTestCase
{
    public function testCategoryPageWorks(): void
    {
        // Crée un nouveau client pour simuler un navigateur web.
        $client = static::createClient();

        // Récupère le service de génération d'URL.
        $urlGeneratorInterface = $client->getContainer()->get('router');

        // Récupère le gestionnaire d'entité pour interagir avec la base de données.
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Récupère le dépôt de l'entité Category pour pouvoir effectuer des requêtes sur cette entité.
        $categoryRepository = $entityManager->getRepository(Category::class);

        // Trouve une catégorie en base de données pour tester la page de cette catégorie.
        $category = $categoryRepository->findOneBy([]);

        // Effectue une requête HTTP GET vers la page de la catégorie trouvée.
        $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('category.index', ['slug' => $category->getSlug()])
        );

        // Assure que la réponse HTTP reçue est un succès (code 200).
        $this->assertResponseIsSuccessful();
        // Vérifie que le code de statut de la réponse est bien 200.
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifie qu'un élément HTML h1 est présent sur la page.
        $this->assertSelectorExists('h1');
        // Vérifie que le texte du h1 contient le nom de la catégorie (en commençant par une majuscule).
        $this->assertSelectorTextContains('h1', 'Catégorie : ' . ucfirst($category->getName()));
    }


    public function testPaginationWorks(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        /** @var EntityManagerInterface */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var CategoryRepository */
        $categoryRepository = $entityManager->getRepository(Category::class);

        /** @var Category */
        $category = $categoryRepository->findOneBy([]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('category.index', ['slug' => $category->getSlug()])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $posts = $crawler->filter('div.card');
        $this->assertEquals(9, count($posts));

        $link = $crawler->selectLink('2')->extract(['href'])[0];
        $crawler = $client->request(Request::METHOD_GET, $link);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $posts = $crawler->filter('div.card');
        $this->assertGreaterThanOrEqual(1, count($posts));
    }


}
