<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function searchByQuery($query)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.partenaire', 'partenaire') // Assurez-vous que 'p.partenaire' correspond à l'association dans votre entité Produit
            ->where('p.nom LIKE :query')
            ->orWhere('p.description LIKE :query')
            ->orWhere('partenaire.marque LIKE :query') // Utilisez l'alias 'partenaire' défini dans la jointure
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
    }

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

        // Appliquer la réduction si un code promo a été utilisé
        $reduction = $session->get('promo_reduction', 0); // Récupère la réduction en pourcentage
        $totalReduit = $total - ($total * $reduction);

        return $this->render('carte/index.html.twig', [
            'items' => $panierWithData,
            'total' => $total,
            'totalReduit' => $totalReduit,
            'reduction' => $reduction > 0, // Booléen pour savoir si une réduction a été appliquée
        ]);
    }

   /* public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }*/

    public function getProduitStats()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as nombreProduits, 
                      CASE 
                        WHEN p.prix < 50 THEN \'moins de 50 £\'
                        WHEN p.prix >= 50 AND p.prix < 100 THEN \'entre 50 et 99£\'
                        WHEN p.prix >= 100 AND p.prix < 200 THEN \'entre 100 et 199£\'
                        ELSE \'200 et plus\' 
                      END as gammePrix')
            ->groupBy('gammePrix')
            ->getQuery();

        return $qb->getResult();
    }

}
