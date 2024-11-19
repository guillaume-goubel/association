<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
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

       /**
     * Trouve les utilisateurs ayant le rôle ROLE_ADMIN
     *
     * @return User[]
     */
    public function findAdmins(string $activityChoice, string $adminChoice): Query
    {
        $stmt = $this->createQueryBuilder('u');
        $stmt->join('u.activities', 'a');

        $stmt->andWhere('u.roles LIKE :role');
        $stmt->setParameter('role', '%"ROLE_ADMIN"%');

        if ($activityChoice && $activityChoice !== 'all') {
            $stmt->andWhere('a.id IN (:activityChoice)');
            $stmt->setParameter('activityChoice', explode(',', $activityChoice));
        }

        if ($adminChoice && $adminChoice !== 'all') {
            $stmt->andWhere('u.id = :adminChoice');
            $stmt->setParameter('adminChoice', $adminChoice);
        }

        $stmt->orderBy('u.lastName', 'ASC')  ;
        return $stmt->getQuery();
    }

    public function adminsList(): array
    {
        $stmt = $this->createQueryBuilder('u');
        $stmt->andWhere('u.roles LIKE :role');
        $stmt->setParameter('role', '%"ROLE_ADMIN"%');

        $stmt->orderBy('u.lastName', 'ASC')  ;
        return $stmt->getQuery()->getResult();
    }

}
