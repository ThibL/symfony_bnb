<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\AdminCommentType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments")
     */
    public function index(CommentRepository $repo, $page,  PaginationService $pagination)
    {
        $pagination->setEntityClass(Comment::class)
                   ->setPage($page)
                   ->setLimit(5);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @param Comment $comment
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/comment/{id}/edit", name="admin_comments_edit")
     */
    public function edit(Comment $comment, Request$request ,ObjectManager $manager) {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', "Le commentaire a bien été modifié");

            return $this->redirectToRoute('admin_comments');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     */
    public function delete(Comment $comment, ObjectManager $manager) {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash('success', "Le commentaire de {$comment->getAuthor()->getFullname()} à bien été supprimé");

        return $this->redirectToRoute('admin_comments');
    }
}
