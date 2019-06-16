<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Services\Pagination;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * Lister tous les commantaires des annonces
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_list")
     */
    public function index(CommentRepository $comments,$page, Pagination $paginationService){

        $paginationService->setEntityClass(Comment::class)
                          ->setLimit(5)
                          ->setPage($page)
                          //->setRoute('admin_comments_list')
                          ;

        return $this->render('admin/comment/index.html.twig',[
            'pagination'=>$paginationService
        ]);

    }

    /**
     * Permet d'editer un commentaire
     * @Route("/admin/comment/{id}/edit",name="admin_comment_edit")
     * 
     * @return Response
     */
    public function edit(Comment $comment, Request $request, ObjectManager $manager){

        $form = $this->createForm(AdminCommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash("success","Le commentaire a bien été enregistré");
            return $this->redirectToRoute('admin_comments_list');
        }

        return $this->render('admin/comment/edit.html.twig',[
            'comment'=>$comment,
            'form'=>$form->createView()
        ]);

    }

    /**
     * Suppression du commentaire
     * @Route("/admin/comment/{id}/delete", name="admin_comment_delete")
     * 
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager){

        $manager->remove($comment);
        $manager->flush();

        $this->addFlash('success',"Le commentaire {$comment->getId()} a bien été supprimé !");

        return $this->redirectToRoute('admin_comments_list');
    }




}
