<?php

namespace App\Repository;

use App\Entity\Baby;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BabyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Baby::class);
    }

    /**
     * Search babies by query.
     *
     * @param string|null $query
     * @return Baby[] Returns an array of Baby objects
     */
    public function findBySearchQuery(?string $query): array
    {
        if (!$query) {
            return $this->findAll();
        }

        return $this->createQueryBuilder('b')
            ->andWhere('b.nom LIKE :query OR b.prenom LIKE :query OR b.sexe LIKE :query OR b.poids LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
}
