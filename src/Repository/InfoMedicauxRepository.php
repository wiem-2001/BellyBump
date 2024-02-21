<?php

namespace App\Repository;

use App\Entity\InfoMedicaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoMedicaux>
 *
 * @method InfoMedicaux|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoMedicaux|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoMedicaux[]    findAll()
 * @method InfoMedicaux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoMedicauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoMedicaux::class);
    }

//    /**
//     * @return InfoMedicaux[] Returns an array of InfoMedicaux objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InfoMedicaux
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
