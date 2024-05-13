<?php

namespace App\Repository;

use App\Entity\Etab;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etab>
 *
 * @method Etab|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etab|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etab[]    findAll()
 * @method Etab[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtabRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etab::class);
    }

//    /**
//     * @return Etab[] Returns an array of Etab objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Etab
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
