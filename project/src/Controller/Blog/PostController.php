<?php

namespace App\Controller\Blog;

use App\Repository\Post\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'post.index' , methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator ,Request $request): Response
    {
        $data = $postRepository->findPublished();
        $posts = $paginator->paginate($data,
            $request->query->getInt('page', 1), /*page number*/
            9 /*limit par page*/);

        return $this->render('pages/blog/index.html.twig',[
            #permet d'utiliser la variables $posts au sein du twig
            'posts' => $posts
            ]);
    }
}
