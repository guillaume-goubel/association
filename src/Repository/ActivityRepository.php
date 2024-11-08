<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    // public function findByUser($userChoice): array
    // {
    //     return $this->createQueryBuilder('a')
    //         ->innerJoin('a.users', 'u')
    //         ->where('u.id = :userChoice')
    //         ->setParameter('userChoice', $userChoice)
    //         ->getQuery()
    //         ->getResult();
    // }

    public function getActivitiesByUser(?string $userChoice = null): array
    {
        $stmt = $this->createQueryBuilder('a');
        $stmt->where('a.isEnabled = :isEnabled');
        $stmt->setParameter('isEnabled', true);

        if ($userChoice && $userChoice != 'all') {
            $stmt->innerJoin('a.users', 'u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', $userChoice);
        }

        // Vous pouvez également ajouter un tri si nécessaire
        $stmt->orderBy('a.name', 'ASC');

        return $stmt->getQuery()->getResult();
    }

}
