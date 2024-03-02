<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/{id}", name="display_comment")
     */
    public function index(CommentRepository $rep,$id): Response
    {
        $comments = $rep->findAll();
        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
            'postId'=>$id
        ]);
    }
    /**
     * @Route("/commentback/{id}", name="display_commentback")
     */
    public function indexbackcomment(CommentRepository $rep,$id): Response
    {
        $comments = $rep->findAll();
        return $this->render('comment/indexbackcomment.html.twig', [
            'comments' => $comments,
            'postId'=>$id
        ]);
    }




    /**
     * @Route("/addcomment/{id}", name="addcomment")
     */
    public function addComment(ManagerRegistry $doctrine, Request $request, $id, PostRepository $rep): Response
    {
        $comment = new Comment();
        $post = $rep->find($id);
        $comment->setPost($post);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('display_post');
        }
        return $this->render('comment/createComment.html.twig', ['f' => $form->createView()]);
    }

    /**
     *
     *
     * @Route("/updatecomment/{id}", name="updatecomment")
     */
    public function updateComment(ManagerRegistry $doctrine, Request $request, CommentRepository $rep, $id): Response
    {
        $comment = $rep->find($id);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('display_post');
        }
        return $this->render('comment/editComment.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/deletecomment/{id}", name="deletecomment")
     */
    public function deleteComment(ManagerRegistry $doctrine, CommentRepository $rep, $id): Response
    {
        $em=$doctrine->getManager();
        $comment=$rep->find($id);
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('display_post');
    }
    /**
     * @Route("/deletecommentback/{id}", name="deletecommentback")
     */
    public function deleteCommentback(ManagerRegistry $doctrine, CommentRepository $rep, $id): Response
    {
        $em=$doctrine->getManager();
        $comment=$rep->find($id);
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('display_commentback');
    }

}