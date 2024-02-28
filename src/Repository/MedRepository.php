<?php

namespace App\Repository;

use App\Entity\Med;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Med>
 *
 * @method Med|null find($id, $lockMode = null, $lockVersion = null)
 * @method Med|null findOneBy(array $criteria, array $orderBy = null)
 * @method Med[]    findAll()
 * @method Med[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Med::class);
    }

//    /**
//     * @return Med[] Returns an array of Med objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Med
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
 /**
     * Recherche les médecins en fonction d'un terme de recherche.
     *
     * @param string $searchTerm
     * @return Medecin[]|null
     */
    public function searchMedecin($searchTerm): ?array
    {
        return $this->createQueryBuilder('m')
            ->where('m.nom LIKE :searchTerm')
            ->orWhere('m.prenom LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    }
}
