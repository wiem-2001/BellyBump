<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    public function findUsersWithMotherRole()
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_MOTHER"%');

        return $qb->getQuery()->getResult();
    }
    public function countBlockedUsers(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.Status = :status')
            ->setParameter('status', 0) // 0 represents blocked users
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countunBlockedUsers(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.Status = :status')
            ->setParameter('status', 1) // 0 represents blocked users
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFavoritedOrParticipatedEvents(User $user): array
    {
        $favoritedEvents = $user->getFavoriteEvents()->toArray();
        $participatedEvents = $user->getEvents()->toArray();

        // Merge and remove duplicates
        $events = array_unique(array_merge($favoritedEvents, $participatedEvents), SORT_REGULAR);

        return $events;
    }

    public function getFavoritedOrParticipatedEventsIds(User $user): array
    {
       $events = $this->getFavoritedOrParticipatedEvents($user);
       $eventsIds = []; 
       foreach ($events as $event){
           $eventsIds[]=$event->getId();
       }
       return $eventsIds ; 
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
