<?php

namespace App\Controller;


use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;




class CarteController extends AbstractController
{
    #[Route('/carte', name: 'app_carte')]
    public function index(SessionInterface $session, ProduitRepository $productRepository): Response
    {
        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $panierWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            } else {
                // Si le produit n'existe pas, le retirer du panier
                unset($panier[$id]);
                $session->set('panier', $panier);
            }
        }

        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['product']->getPrix() * $item['quantity'];
            $total += $totalItem;
        }

        return $this->render('carte/index.html.twig', [
            'items' => $panierWithData,
            'total' => $total
        ]);
    }



    #[Route('/panier/add/{id}', name: 'cart_add')]
public function add($id, SessionInterface $session)
{

$panier=$session->get('panier',[]);
    if (!empty($panier[$id])) {
        $panier[$id]++;
    } else {
        $panier[$id] = 1;
    }


$session->set('panier',$panier);
//dd($session->get('panier'));
    return $this->redirectToRoute('app_carte');

}
    #[Route('/panier/remove/{id}', name: 'cart_remove')]
public function remove($id,SessionInterface $session){
        $panier =$session->get('panier',[]);
        if(!empty($panier[$id])){

            unset($panier[$id]);
        }
        $session->set('panier',$panier);
        return $this->redirectToRoute("app_carte");


}

    #[Route('/panier/update/{id}/{change}', name: 'cart_update_quantity')]
    public function updateQuantity($id, $change, SessionInterface $session): JsonResponse
    {
        $panier = $session->get('panier', []);
        if (isset($panier[$id])) {
            $panier[$id] += $change;
            if ($panier[$id] <= 0) {
                unset($panier[$id]); // Supprimer l'article s'il n'y a plus de quantité
            }
            $session->set('panier', $panier);
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false]);
    }



    #[Route('/panier/promo', name: 'apply_promo', methods: ["POST"])]
    public function applyPromo(Request $request, SessionInterface $session): Response
    {
        $code = $request->request->get('promo_code');
        // Exemple de code promo et de réduction associée
        $validPromoCodes = [
            'PROMO2023' => 0.1, // 10% de réduction
        ];

        if (array_key_exists($code, $validPromoCodes)) {
            $panier = $session->get('panier', []);
            $reduction = $validPromoCodes[$code];

            $session->set('promo_reduction', $reduction);
            $this->addFlash('success', 'Code promo appliqué avec succès !');
        } else {
            $this->addFlash('error', 'Code promo invalide.');
        }

        return $this->redirectToRoute('app_carte');
    }
    #[Route('/process_payment', name: 'process_payment', methods: ["POST"])]
    public function processPayment(SessionInterface $session): RedirectResponse
    {
        // Ici, vous simulez le processus de paiement. Dans un cas réel, vous intégreriez avec une API de paiement.
        // Pour la simulation, vous pouvez juste nettoyer le panier et rediriger vers une page de succès.

        $session->set('panier', []); // Nettoyer le panier après "paiement"
        $this->addFlash('success', 'Votre paiement a été traité avec succès !'); // Ajouter un message de succès

        return $this->redirectToRoute('app_carte'); // Rediriger vers la page d'accueil ou une page de confirmation
    }



    #[Route('/payment', name: 'payment_form')]
    public function paymentForm(): Response
    {

        $this->addFlash('success', 'Paiement traité avec succès !');
        return $this->render('Carte/form.html.twig');
    }

}



