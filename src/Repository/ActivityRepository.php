<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Activity;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    public function getFirstActivityByUser(?string $userChoice = null): ?Activity
    {
        $stmt = $this->createQueryBuilder('a')
            ->where('a.isEnabled = :isEnabled')
            ->setParameter('isEnabled', true);
    
        if ($userChoice && $userChoice !== 'all') {
            $stmt->innerJoin('a.users', 'u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', $userChoice);
        }
    
        // Ajout d'un tri et d'une limite pour obtenir la première activité
        $stmt->orderBy('a.name', 'ASC')
            ->setMaxResults(1);
    
        return $stmt->getQuery()->getOneOrNullResult();
    }

    /**
     * Récupère les activités par mois et année des événements associés.
     *
     * @param int $month Le mois de l'événement (1-12)
     * @param int $year L'année de l'événement
     * @return Activity[] Retourne un tableau d'activités filtrées
     */
    public function findByMonthAndYearForAgenda(?string $monthChoice, ?string $yearChoice): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0);
    
        $stmt = $this->createQueryBuilder('a')
            ->innerJoin('a.events', 'e')
            ->where('e.dateStartAt >= :today')
            ->setParameter('today', $today)
            ->andWhere('a.isEnabled = true');
    
        if ($yearChoice && $yearChoice !== 'all') {
            $stmt->andWhere('YEAR(e.dateStartAt) = :year')
            ->setParameter('year', $yearChoice);
        }
    
        if ($monthChoice && $monthChoice !== 'all') {
            $stmt->andWhere('MONTH(e.dateStartAt) = :month')
            ->setParameter('month', $monthChoice);
        }
    
        $stmt->orderBy('a.name', 'ASC');
    
        return $stmt->getQuery()->getResult();
    }

    /**
     * Récupère les activités par mois et année des événements associés.
     *
     * @param int $month Le mois de l'événement (1-12)
     * @param int $year L'année de l'événement
     * @return Activity[] Retourne un tableau d'activités filtrées
     */
    public function findByMonthAndYearForArchive(?string $monthChoice, ?string $yearChoice): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0);
    
        $stmt = $this->createQueryBuilder('a')
            ->innerJoin('a.events', 'e')
            ->where('e.dateStartAt < :today')
            ->setParameter('today', $today)
            ->andWhere('a.isEnabled = true');
    
        if ($yearChoice) {
            $stmt->andWhere('YEAR(e.dateStartAt) = :year')
            ->setParameter('year', $yearChoice);
        }
    
        if ($monthChoice && $monthChoice !== 'all') {
            $stmt->andWhere('MONTH(e.dateStartAt) = :month')
            ->setParameter('month', $monthChoice);
        }
    
        $stmt->orderBy('a.name', 'ASC');
    
        return $stmt->getQuery()->getResult();
    }

        /**
     * Récupère les activités par mois et année des événements associés.
     *
     * @param int $month Le mois de l'événement (1-12)
     * @param int $year L'année de l'événement
     * @return Activity[] Retourne un tableau d'activités filtrées
     */
    public function findByMonthAndYearForCalendar(?string $yearChoice): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0);
    
        $stmt = $this->createQueryBuilder('a')
            ->innerJoin('a.events', 'e')
            ->where('a.isEnabled = true');
    
        if ($yearChoice == 'yearDepth') {
            $yearDefault = (new \DateTime())->format('Y');
            $monthDefault = (new \DateTime())->format('m');
            $startDate = new \DateTime("{$yearDefault}-{$monthDefault}-01");
            $endDate = (clone $startDate)->modify('+12 months')->modify('-1 day'); // Dernier jour de la période de 12 mois
            
            // Filtrage par la plage de dates
            $stmt->andWhere('e.dateStartAt BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }else{
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }
        
        $stmt->orderBy('a.name', 'ASC');
    
        return $stmt->getQuery()->getResult();
    }

    /**
     * Retourne les activités ayant au moins un utilisateur associé
     */
    public function findActivitiesWithUsers(): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.users', 'u') 
            ->where('u IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function getAllActivitiesCount(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = " SELECT COUNT(a.id) as total FROM activity a;
            ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAssociative();
        return $result;
    }

    public function getAnimatorsByActivity($activityId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = " SELECT DISTINCT(ea.animator_id) FROM `activity` as a 
                 INNER JOIN event e
                 ON e.activity_id = a.id
 
                 INNER JOIN event_animator ea 
                 ON ea.event_id = e.id
 
                 WHERE a.id = $activityId
            ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

}
