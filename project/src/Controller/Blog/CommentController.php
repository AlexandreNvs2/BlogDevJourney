<?php

namespace App\Controller\Blog;

use App\Entity\Post\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/comment/{id}', name: 'comment.delete')]
    #[IsGranted(
        attribute: new Expression('is_granted("ROLE_USER") and user === comment.getAuthor()'),
        subject: 'comment',
    )]    public function delete(Comment $comment, EntityManagerInterface $entityManager, Request $request): Response
    {
        $params = ['slug' => $comment->getPost()->getSlug()];
        if($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Votre commentaire a bien été supprimé.');

        }


        return $this->redirectToRoute('post.show', $params);
    }
}