<?php

namespace App\Controller\Blog;

use App\Entity\Post\Post;
use App\Model\SearchData;
use App\Repository\Post\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchType;
use App\Form\CommentType;
use App\Entity\Post\Comment;


class PostController extends AbstractController
{
    #[Route('/', name: 'post.index', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        Request $request
    ): Response {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $postRepository->findBySearch($searchData);

            return $this->render('pages/post/index.html.twig', [
                'form' => $form->createView(),
                'posts' => $posts
            ]);
        }

        return $this->render('pages/post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $postRepository->findPublished($request->query->getInt('page', 1))
        ]);
    }

    /**
     * Cette route capte un 'slug' de l'URL et le passe à la méthode show().
     * @param Post $post
     * @return Response
     */
    // Cette route capte un 'slug' de l'URL et le passe à la méthode show().
    #[Route('/article/{slug}', name: 'post.show', methods: ['GET','POST'])]
    public function show(Post $post, EntityManagerInterface $manager, Request $request): Response
    {

        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $comment = new Comment;
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());

            $comment->setContent($form->getData()['content']);
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash("success", "Votre commentaire à été poster avec succès !");

            return $this->redirectToRoute('post.show', ['slug' => $post->getSlug()]);
        }
        // ParamConverter automatiquement convertit 'slug' en entité Post.
        // Si aucun Post trouvé pour 'slug', une erreur 404 est générée.

        // Génère le HTML en utilisant 'post' trouvé pour le template spécifié.
        return $this->render('pages/post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView()

        ],
        );
    }
}
