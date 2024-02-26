<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="display_post")
     */
    public function index(PostRepository $rep): Response
    {
        $posts = $rep->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }
    /**
     * @Route("/postback", name="display_postback")
     */
    public function indexback(PostRepository $rep): Response
    {
        $posts = $rep->findAll();
        return $this->render('post/indexback.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/addpost", name="addpost")
     */
    public function addPost(ManagerRegistry $doctrine, Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file instanceof UploadedFile) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileExtension = $file->guessExtension();

                if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                    $this->addFlash('error', 'Only JPG, JPEG, and PNG files are allowed.');
                    return $this->redirectToRoute('addpost');
                }

                // Handle file upload and entity persisting
                $fileName = md5(uniqid()) . '.' . $fileExtension;
                $file->move($this->getParameter('images_directory'), $fileName);
                $post->setImage($fileName);


                $em = $doctrine->getManager();
                $em->persist($post);
                $em->flush();
                return $this->redirectToRoute('display_post');
            }
        }
        return $this->render('post/createPost.html.twig', ['f' => $form->createView()]);
    }




    /**
     * @Route("/updatepost/{id}", name="updatepost")
     */
    public function updatePost(ManagerRegistry $doctrine, Request $request, PostRepository $rep, $id): Response
    {

        $post = $rep->find($id);
        $oldFileName = $post->getImage();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $file = $form->get('image')->getData();
            if($file instanceof UploadedFile) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileExtension = $file->guessExtension();

                if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                    $this->addFlash('error', 'Only JPG, JPEG, and PNG files are allowed.');
                    return $this->redirectToRoute('updatepost', ['id' => $id]);
                }

                // Delete the old file if it exists

                $oldFilePath = $this->getParameter('images_directory') . '/' . $oldFileName;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
                // Handle file upload and entity updating
                $fileName = md5(uniqid()) . '.' . $fileExtension;
                $file->move($this->getParameter('images_directory'), $fileName);
                $post->setImage($fileName);
            }

            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('display_post');
        }
        return $this->render('post/editPost.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/deletepost/{id}", name="deletepost")
     */
    public function deletePost(ManagerRegistry $doctrine, PostRepository $rep, $id): Response
    {
        $em=$doctrine->getManager();
        $post=$rep->find($id);
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('display_post');
    }
    /**
     * @Route("/deletepostback/{id}", name="deletepostback")
     */
    public function deletePostback(ManagerRegistry $doctrine, PostRepository $rep, $id): Response
    {
        $em=$doctrine->getManager();
        $post=$rep->find($id);
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('display_postback');
    }

}