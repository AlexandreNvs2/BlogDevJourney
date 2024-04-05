<?php

namespace App\Tests\Functionnal\Post;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PostTest extends WebTestCase
{
    public function testBlogPageWork(): void
    {
        // Crée un client pour simuler un navigateur et effectue une requête GET vers la racine du site.
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');

        // Vérifie que la page charge correctement avec un code de statut HTTP 200.
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Confirme la présence d'une balise <h1> et que son contenu inclut 'BlogDevJourney'.
        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', 'BlogDevJourney');
    }

    public function testPagination(): void
    {
        // Crée un client pour simuler un navigateur et effectue une requête GET vers la racine du site.
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/');

        // Vérifie que la réponse est un succès avec un code 200.
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Sélectionne les divs avec classe "card" et vérifie le nombre de card sur la première page.
        $posts = $crawler->filter('div.card');
        $this->assertCount(9, $posts);

        // Trouve le lien "2" (pour la page 2) et extrait son attribut href.
        $link = $crawler->selectLink('2')->extract(['href'])[0];
        // Navigue vers la page 2 en utilisant le lien extrait.
        $crawler = $client->request(Request::METHOD_GET, $link);

        // Vérifie à nouveau que la réponse HTTP est un succès (code 200).
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Sélectionne et compte les "cards" sur la page 2 pour s'assurer d'au moins 1 post.
        $posts = $crawler->filter('div.card');
        $this->assertGreaterThanOrEqual(1, count($posts));
    }

}