<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Core\Security;

class PostController extends AbstractController
{

    /**
     * @Route("/post", name="display_post")
     */
    public function index(PostRepository $rep, Request $request,PaginatorInterface $paginator,Security $security): Response
    {

        $user=$security->getUser();
        $pagination =$paginator->paginate(
            $rep->paginationQuery(),
            $request->query->get('page',1),
            2
        );
        return $this->render('post/index.html.twig', [

            'pagination'=>$pagination,
            'user'=>$user
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
    public function addPost(ManagerRegistry $doctrine, Request $request,Security $security): Response
    {
        $user=$security->getUser();
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->add('ajouter',SubmitType::class);
        $form->handleRequest($request);
        $badWords = ['idiot', 'bete', 'naif','debile'];
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($badWords as $word) {
                if (stripos($post->getTitle(), $word) !== false) {
                    $this->addFlash('error', 'Your post contains inappropriate content.');
                    return $this->redirectToRoute('addpost');
                }
            }

            // Vérifier si le contenu contient des mots inappropriés
            foreach ($badWords as $word) {
                if (stripos($post->getContent(), $word) !== false) {
                    $this->addFlash('error', 'Your post contains inappropriate content.');
                    return $this->redirectToRoute('addpost');
                }
            }
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
                $file->move($this->getParameter('images_directory_post'), $fileName);
                $post->setImage($fileName);
                $post->setAuteur($user);

                $em = $doctrine->getManager();
                $em->persist($post);
                $em->flush();
                return $this->redirectToRoute('display_post');
            }
        }
        return $this->render('post/createPost.html.twig', ['f' => $form->createView(),'user'=>$user]);
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

                $oldFilePath = $this->getParameter('images_directory_post') . '/' . $oldFileName;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
                // Handle file upload and entity updating
                $fileName = md5(uniqid()) . '.' . $fileExtension;
                $file->move($this->getParameter('images_directory_post'), $fileName);
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
    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, PostRepository $postRepository): Response
    {
        $keyword = $request->query->get('keyword');

        if ($keyword) {
            $posts = $postRepository->searchByKeyword($keyword);
        } else {
            $posts = [];
        }

        return $this->render('post/search_results.html.twig', [
            'posts' => $posts,
            'keyword' => $keyword,
        ]);
    }
    /**
     * @Route("/export/pdf", name="export_pdf")
     */
    public function exportPdf(PostRepository $postRepository): Response
    {
        // Récupérer la liste des posts depuis le repository
        $posts = $postRepository->findAll();

        // Générer le contenu PDF
        $pdfContent = $this->renderView('post/pdf_export.html.twig', [
            'posts' => $posts,
        ]);

        // Créer une instance de Dompdf et définir les options
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Charger le contenu PDF dans Dompdf
        $dompdf->loadHtml($pdfContent);

        // Rendre le PDF
        $dompdf->render();

        // Envoyer le PDF au navigateur en tant que réponse
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }



}