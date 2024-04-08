<?php

namespace App\Tests\Functionnal\Tag;

use App\Entity\Post\Tag;
use App\Repository\Post\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TagTest extends WebTestCase
{
    public function testPageWorks(): void
    {
        // Crée un nouveau client HTTP, simulant un navigateur pour effectuer des requêtes HTTP.
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        // Récupère le service de génération d'URL pour créer des URL à partir des noms de routes.
        $urlGeneratorInterface = $client->getContainer()->get('router');

        /** @var EntityManagerInterface */
        // Récupère l'EntityManager de Doctrine pour interagir avec la base de données.
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var TagRepository */
        // Récupère le dépôt (repository) de l'entité Tag pour effectuer des requêtes spécifiques à cette entité.
        $tagRepository = $entityManager->getRepository(Tag::class);

        /** @var Tag */
        // Trouve un objet Tag dans la base de données. findOneBy([]) retournera le premier Tag trouvé.
        $tag = $tagRepository->findOneBy([]);

        // Effectue une requête HTTP GET sur la route 'tag.index' en utilisant le slug du Tag trouvé comme paramètre.
        $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('tag.index', ['slug' => $tag->getSlug()])
        );

        // Assure que la réponse HTTP obtenue est un succès, c'est-à-dire un code de statut 200.
        $this->assertResponseIsSuccessful();
        // Vérifie également que le code de statut HTTP est bien 200.
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifie l'existence d'un élément h1 dans la réponse.
        $this->assertSelectorExists('h1');
        // Vérifie que le contenu du h1 contient le nom du tag, formaté avec la première lettre en majuscule.
        $this->assertSelectorTextContains('h1', 'Etiquette : ' . ucfirst($tag->getName()));
            }


    public function testPaginationWorks(): void
    {
        // Création d'un nouveau client pour simuler un navigateur web et interagir avec l'application Symfony.
        $client = static::createClient();

        // Récupération du service UrlGeneratorInterface pour générer des URLs dans l'application.
        $urlGeneratorInterface = $client->getContainer()->get('router');

        // Récupération du gestionnaire d'entités EntityManager pour interagir avec la base de données.
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Utilisation du repository des tags pour pouvoir effectuer des requêtes sur les entités Tag.
        $tagRepository = $entityManager->getRepository(Tag::class);

        // Récupération d'un tag spécifique depuis la base de données pour le tester. Ici, on prend le premier trouvé.
        $tag = $tagRepository->findOneBy([]);

        // Faire une requête HTTP GET vers la route 'tag.index', en utilisant le slug du tag récupéré comme paramètre.
        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('tag.index', ['slug' => $tag->getSlug()])
        );

        // Vérification que la réponse HTTP obtenue est un succès (code 200).
        $this->assertResponseIsSuccessful();

        // Vérification que le code de statut HTTP de la réponse est bien 200 (OK).
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Utilisation de Crawler pour filtrer les éléments du DOM et compter le nombre de div avec la classe 'card'.
        $posts = $crawler->filter('div.card');

        // On s'attend à avoir exactement 9 posts affichés sur la page.
        $this->assertEquals(9, count($posts));

        // Naviguer vers la page 2 de la pagination en cliquant sur le lien '2'.
        $link = $crawler->selectLink('2')->extract(['href'])[0];

        // Effectue une requête GET vers la page 2.
        $crawler = $client->request(Request::METHOD_GET, $link);

        // Répétition des vérifications pour la page 2 pour s'assurer que la pagination fonctionne correctement.
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Filtrer à nouveau pour compter le nombre de posts sur la page 2.
        $posts = $crawler->filter('div.card');

        // On s'attend à avoir au moins 1 post affiché sur la page 2.
        $this->assertGreaterThanOrEqual(1, count($posts));

    }
}
