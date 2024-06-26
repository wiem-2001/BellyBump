<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Entity\Produit;
use App\Form\PartenaireType;
use App\Form\ProduitType;
use App\Repository\PartenaireRepository;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProductController extends AbstractController
{

    #[Route('/shop', name: 'back_product')]
    public function indexback(Security $security): Response
    {
        $user=$security->getUser();
        $product=$this->getDoctrine()->getRepository(produit::class)->findAll();
        return $this->render('product/showProduct.html.twig', [
            'pr'=>$product,
            'user'=>$user
        ]);
    }

    #[Route('/Showproductback', name: 'show_back_product')]
    public function showProductback(Security $security): Response
    {
        $user=$security->getUser();

        $product=$this->getDoctrine()->getRepository(produit::class)->findAll();


        return $this->render('product/show_product_back.html.twig', [
            'pr'=>$product,
            'user'=>$user

        ]);
    }
    #[Route('/produit/new', name: 'produit_new')]
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('show_back_product');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/removeProduct/{id}', name: 'remove_product')]

    public function deleteProduct(ManagerRegistry $managerRegistry,ProduitRepository $repository,$id)
    {
        $product= $repository->find($id);
        $em= $managerRegistry->getManager();

        $em->remove($product);
        $em->flush();


       return $this->redirectToRoute("show_back_product");



    }

    #[Route('/updateproduct/{id}', name: 'update_product')]
    public function updateproduct(Request $request,ManagerRegistry $managerRegistry,$id,produitRepository $repository): Response
    {
        $product= $this->getDoctrine()->getManager()->getRepository(produit::class)->find($id);
        $form = $this->createForm(produitType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isvalid()) {
            $em = $managerRegistry->getManager();
            //$em->persist($partner);
            $em->flush();
            return $this->redirectToRoute("show_back_product");
        }

        return $this->renderForm("product/updateProduct.html.twig"
            ,array('form'=>$form));

    }

    #[Route('/shop/search', name: 'shop_search')]
    public function search(Request $request,Security $security)
    { 
        $user=$security->getUser();
        $query = $request->query->get('query');
        dump($query); // Temporaire, pour débogage

        // Effectuez la recherche dans votre base de données
        // Exemple avec un repository de produit
        $products = $this->getDoctrine()->getRepository(Produit::class)->searchByQuery($query);

        // Renvoyez le même template que la boutique mais avec les produits filtrés
        return $this->render('product/showProduct.html.twig', [
            'pr' => $products,
            'user'=>$user
        ]);
    }
    #[Route('/shop/searchBack', name: 'shop_searchBack')]
    public function searchBack(Request $request)
    { $query = $request->query->get('query');
        dump($query); // Temporaire, pour débogage

        // Effectuez la recherche dans votre base de données
        // Exemple avec un repository de produit
        $products = $this->getDoctrine()->getRepository(Produit::class)->searchByQuery($query);

        // Renvoyez le même template que la boutique mais avec les produits filtrés
        return $this->render('product/show_Product_back.html.twig', [
            'pr' => $products
        ]);
    }



/////pour les statistique
    #[Route('/Productstats', name: 'produit_stats')]
    public function index(ProduitRepository $produitRepository): Response
    {


        $stats = $produitRepository->getProduitStats();

        $labels = [];
        $data = [];
        foreach ($stats as $stat) {
            $labels[] = $stat['gammePrix'];
            $data[] = $stat['nombreProduits'];
        }

        return $this->render('product/stat.html.twig', [
            'labels' => $labels,
            'data' => $data,
        ]);
    }

}
