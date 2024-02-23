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

class ProductController extends AbstractController
{

    #[Route('/shop', name: 'back_product')]
    public function indexback(): Response
    {
        $product=$this->getDoctrine()->getRepository(produit::class)->findAll();


        return $this->render('product/showProduct.html.twig', [
            'pr'=>$product
        ]);
    }
    #[Route('/Showproductback', name: 'show_back_product')]
    public function showProductback(): Response
    {
        $product=$this->getDoctrine()->getRepository(produit::class)->findAll();


        return $this->render('product/show_product_back.html.twig', [
            'pr'=>$product
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


       return $this->redirectToRoute("back_product");



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
            return $this->redirectToRoute("back_product");
        }

        return $this->renderForm("product/updateProduct.html.twig"
            ,array('form'=>$form));

    }

    #[Route('/shop/search', name: 'shop_search')]
    public function search(Request $request)
    { $query = $request->query->get('query');
        dump($query); // Temporaire, pour débogage

        // Effectuez la recherche dans votre base de données
        // Exemple avec un repository de produit
        $products = $this->getDoctrine()->getRepository(Produit::class)->searchByQuery($query);

        // Renvoyez le même template que la boutique mais avec les produits filtrés
        return $this->render('product/showProduct.html.twig', [
            'pr' => $products
        ]);
    }
    #[Route('/shop/search', name: 'shop_search')]
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

}
