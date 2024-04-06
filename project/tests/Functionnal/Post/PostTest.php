<?php

namespace App\Tests\Functionnal\Post;

use App\Entity\Post\Post;
use App\Repository\Post\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface
use JetBrains\PhpStorm\NoReturn;


class PostTest extends WebTestCase
{
    public function testPostPageWorks(): void
    {
        // Crée un nouveau client de navigateur pour simuler un navigateur web dans les tests
        $client = static::createClient();

        // Récupère le service de génération d'URL pour créer des URL dans les tests
        /** @var UrlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        // Récupère le gestionnaire d'entités pour accéder à la base de données
        /** @var EntityManagerInterface */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Récupère le repository de l'entité Post pour effectuer des requêtes dans la base de données
        /** @var PostRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        // Récupère un post depuis la base de données
        /** @var Post */
        $post = $postRepository->findOneBy([]);

        // Effectue une requête HTTP GET sur la route de l'affichage d'un post, en utilisant son slug
        $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('post.show', ['slug' => $post->getSlug()])
        );

        // Assure que la réponse reçue est réussie (status code 200)
        $this->assertResponseIsSuccessful();
        // Assure que le code de statut de la réponse est bien HTTP 200
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifie qu'un sélecteur CSS 'h1' existe dans la réponse
        $this->assertSelectorExists('h1');
        // Vérifie que le texte contenu dans le sélecteur 'h1' contient le titre du post (avec la première lettre en majuscule)
        $this->assertSelectorTextContains('h1', ucfirst($post->getTitle()));
    }



    public function testReturnToBlogWorks(): void
    {
        // Crée un nouveau client pour simuler un navigateur web
        $client = static::createClient();

        // Récupère le service de génération d'URL pour créer des URL dans les tests
        /** @var UrlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        // Récupère le gestionnaire d'entités pour accéder à la base de données
        /** @var EntityManagerInterface */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Récupère le repository de l'entité Post pour effectuer des requêtes dans la base de données
        /** @var PostRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        // Récupère un post depuis la base de données
        /** @var Post */
        $post = $postRepository->findOneBy([]);

        // Effectue une requête GET sur la route d'affichage d'un post et récupère le crawler pour analyser la réponse
        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('post.show', ['slug' => $post->getSlug()])
        );

        // Vérifie que la réponse est réussie
        $this->assertResponseIsSuccessful();
        // Vérifie que le code de statut de la réponse est HTTP 200
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Utilise le crawler pour trouver le lien de retour au blog et récupère son URI
        $link = $crawler->selectLink('Retourner au blog')->link()->getUri();

        // Effectue une nouvelle requête GET sur le lien de retour au blog
        $crawler = $client->request(Request::METHOD_GET, $link);

        // Vérifie que la réponse à la page d'accueil du blog est réussie
        $this->assertResponseIsSuccessful();
        // Vérifie que le code de statut de la réponse est HTTP 200
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        // Assure que la route correspond bien à la page d'index du blog
        $this->assertRouteSame('post.index');
    }
}

