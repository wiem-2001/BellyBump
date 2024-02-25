<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function MotherParticipatedEvents(User $mother){
        return $this->createQueryBuilder('e')
        ->innerJoin('e.reservation', 'u')
        ->andWhere('u = :mother')
        ->setParameter('mother', $mother)
        ->getQuery()
        ->getResult();
    }

    public function MotherNotParticipatedEvents(User $mother){
        return $this->createQueryBuilder('e')
            ->leftJoin('e.reservation', 'u')
            ->andWhere('u != :mother OR u IS NULL')
            ->setParameter('mother', $mother)
            ->getQuery()
            ->getResult();
    }

    public function findRealizedEvents(){
        return $this->createQueryBuilder('e')
            ->andWhere('e.day < :today')
            ->setParameter('today', new \DateTime())
            ->getQuery()
            ->getResult();
    }
    public function findNotRealizedEvents(){
        return $this->createQueryBuilder('e')
            ->andWhere('e.day > :today')
            ->setParameter('today', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function sortEventsByDate(User $mother){
        return $this->createQueryBuilder('e')
            ->leftJoin('e.reservation', 'u')
            ->andWhere('u != :mother OR u IS NULL')
            ->setParameter('mother', $mother)
            ->orderBy('e.day', 'ASC')
            ->addOrderBy('e.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function sortEventsByCoach(User $mother){
        return $this->createQueryBuilder('e')
            ->leftJoin('e.coach','c')
            ->leftJoin('e.reservation','u')
            ->andWhere('u != :mother OR u IS NULL')
            ->setParameter('mother', $mother)
            ->addSelect('c')
            ->orderBy('c.firstname','ASC')
            ->addOrderBy('c.lastname','ASC')
            ->getQuery()->getResult();
    }




//    /**
//     * @return Event[] Returns an array of Event objects
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

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
