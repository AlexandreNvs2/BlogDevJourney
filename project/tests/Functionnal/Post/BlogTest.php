<?php

namespace App\Tests\Functionnal\Post;


use App\Entity\Post\Tag;
use App\Entity\Post\Post;
use App\Entity\Post\Category;
use App\Repository\Post\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class BlogTest extends WebTestCase
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

    public function testFilterSystemWorks(): void
    {
        // Création d'un client pour simuler un navigateur
        $client = static::createClient();

        // Récupération du service de génération d'URL
        $urlGeneratorInterface = $client->getContainer()->get('router');

        // Récupération du gestionnaire d'entités
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Récupération du repository des Posts et Categories
        $postRepository = $entityManager->getRepository(Post::class);
        $categoryRepository = $entityManager->getRepository(Category::class);

        // Trouver un Post et récupérer son premier Tag
        $post = $postRepository->findOneBy([]);
        $tag = $post->getTags()[0];

        // Trouver une Category
        $category = $categoryRepository->findOneBy([]);

        // Faire une requête GET à la page d'index des Posts
        $crawler = $client->request(Request::METHOD_GET, $urlGeneratorInterface->generate('post.index'));

        // Vérification de la réponse HTTP
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Définir les termes de recherche à partir du titre du Post et du nom du Tag
        $searchs = [
            substr($post->getTitle(), 0, 3),
            substr($tag->getName(), 0, 3)
        ];

        // Itérer sur les termes de recherche
        foreach ($searchs as $search) {
            // Soumettre le formulaire de recherche avec le terme de recherche et une catégorie sélectionnée
            $form = $crawler->filter('form[name=search]')->form([
                'search[q]' => $search,
                'search[categories][0]' => 1
            ]);

            $crawler = $client->submit($form);

            // Vérifier à nouveau la réponse HTTP
            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            $this->assertRouteSame('post.index');

            // Compter le nombre de Posts retournés
            $nbPosts = count($crawler->filter('div.card'));
            $posts = $crawler->filter('div.card');
            $count = 0;

            // Itérer sur chaque Post et vérifier si le terme de recherche est présent dans le titre ou le Tag
            foreach ($posts as $index => $post) {
                $title = $crawler->filter("div.card h5")->getNode($index);
                if (
                    str_contains($title->textContent, $search) ||
                    str_contains($tag->getName(), $search)
                ) {
                    $postCategories = $crawler->filter('div.card div.badges')->getNode($index)->childNodes;

                    for ($i = 1; $i < $postCategories->count(); $i++) {
                        $postCategory = $postCategories->item($i);
                        $name = trim($postCategory->textContent);

                        if ($name === $category->getName()) {
                            $count++;
                        }
                    }
                }
            }

            // Vérifier si le nombre de Posts avec la catégorie correcte correspond au nombre total de Posts retournés
            $this->assertEquals($nbPosts, $count);
        }
    }


    public function testFilterSystemReturnsNoItems(): void
    {
        // Création d'un client pour simuler un navigateur
        $client = static::createClient();

        // Récupération du service de génération d'URL
        $urlGeneratorInterface = $client->getContainer()->get('router');

        // Faire une requête GET à la page d'index des Posts
        $crawler = $client->request(Request::METHOD_GET, $urlGeneratorInterface->generate('post.index'));

        // Vérification de la réponse HTTP
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Soumettre le formulaire de recherche avec un terme de recherche qui ne correspond à aucun Post
        $form = $crawler->filter('form[name=search]')->form([
            'search[q]' => 'aazzeerrttyy'
        ]);

        $crawler = $client->submit($form);

        // Vérifier à nouveau la réponse HTTP
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('post.index');

        // Vérifier que le formulaire de recherche est présent mais aucun Post n'est retourné
        $this->assertSelectorExists('form[name=search]');
        $this->assertSelectorNotExists('div.card');
    }


}