<?php

namespace App\Controller\Blog;

use App\Repository\Post\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'post.index' , methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findPublished();
        return $this->render('pages/blog/index.html.twig',[
            #permet d'utiliser la variables $posts au sein du twig
            'posts' => $posts
            ]);
    }
}
