<?php

namespace App\Controller\Blog;

use App\Entity\Post\Tag;
use App\Repository\Post\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/etiquettes')]
class TagController extends AbstractController
{
    #[Route('/{slug}', name: 'tag.index', methods: ['GET'])]
    public function index(Tag $tag, PostRepository $postRepository, Request $request): Response
    {

        return $this->render('pages/tag/index.html.twig', [
            'tag' => $tag,
            'posts' => $postRepository->findPublished($request->query->getInt('page', 1), null, $tag)
        ]);
    }
}
