<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    public function getEventListforAdmin(string $yearChoice, string $monthChoice, string $creatorChoice, 
    string $activityChoice, string $dateChoice, string $isPassedChoice, 
    string $isCanceledChoice, string $animatorChoice, string $isEnabledChoice): Query
    {
        $stmt = $this->createQueryBuilder('e');
        $stmt->leftjoin('e.activity', 'a');
        $stmt->leftjoin('e.animators', 'an');

        if ($yearChoice && $yearChoice != 'all') {
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }

        if ($monthChoice && $monthChoice != 'all') {
            $stmt->andwhere('MONTH(e.dateStartAt) = :month');
            $stmt->setParameter('month', $monthChoice);
        }

        if ($creatorChoice && $creatorChoice != 'all') {
            $stmt->andwhere('e.user = :user');
            $stmt->setParameter('user', $creatorChoice);
        }

        if ($animatorChoice && $animatorChoice != 'all') {
            $stmt->andwhere('an.id = :animator');
            $stmt->setParameter('animator', $animatorChoice);
        }

        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('a.id = :activityChoice');
            $stmt->setParameter('activityChoice', $activityChoice);
        }

        if ($dateChoice == 'dateStartAt') {
            $stmt->addOrderBy('e.dateStartAt', 'ASC');
        }else{
            $stmt->addOrderBy('e.createdAt', 'DESC');
        }

        if ($isPassedChoice != 'all') {
            $now = new \DateTime();
            if ($isPassedChoice == 'isPassed') {
                // Si l'événement est "passé" (dateEndAt est strictement inférieure à aujourd'hui)
                $stmt->andWhere('e.dateEndAt < :today');
            } else {
                // Si l'événement n'est pas "passé" (dateEndAt est aujourd'hui ou dans le futur)
                $stmt->andWhere('e.dateEndAt >= :today');
            }
            $stmt->setParameter('today', $now->format('Y-m-d')); // Comparaison sur la date
        }

        if ($isCanceledChoice != 'all') {
            if ($isCanceledChoice == 'isCanceled') {
                // Vérification si la date de fin est avant aujourd'hui
                $stmt->andWhere('e.isCanceled = TRUE');
            } else {
                // Vérification si la date de fin est après ou égale à aujourd'hui
                $stmt->andWhere('e.isCanceled = FALSE');
            }
        }

        if ($isEnabledChoice != 'all') {
            if ($isEnabledChoice == 'isEnabled') {
                // Vérification si la date de fin est avant aujourd'hui
                $stmt->andWhere('e.isEnabled = TRUE');
            } else {
                // Vérification si la date de fin est après ou égale à aujourd'hui
                $stmt->andWhere('e.isEnabled = FALSE');
            }
        }

        return $stmt->getQuery();
    }

    public function findLastEventsforAdmin(int $limit): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'ASC') 
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getEventListforAgenda(string $yearChoice, string $monthChoice, string $activityChoice):Query
    {
        $today = new \DateTime();
        $today->setTime(0, 0);

        $stmt = $this->createQueryBuilder('e');
        $stmt->join('e.activity', 'a');
        $stmt->andWhere('e.dateStartAt >= :today');   
        $stmt->setParameter('today', $today);

        if ($yearChoice && $yearChoice != 'all') {
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }

        if ($monthChoice && $monthChoice != 'all') {
            $stmt->andwhere('MONTH(e.dateStartAt) = :month');
            $stmt->setParameter('month', $monthChoice);
        }

        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('a.id = :activityChoice');
            $stmt->setParameter('activityChoice', $activityChoice);
        }

        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        return $stmt->getQuery();
    }

    public function getEventListforArchive(string $yearChoice, string $monthChoice, string $activityChoice): Query
    {
        $today = new \DateTime();
        $today->setTime(0, 0);

        $stmt = $this->createQueryBuilder('e');
        $stmt->join('e.activity', 'a');
        $stmt->andWhere('e.dateStartAt < :today');   
        $stmt->setParameter('today', $today);

        if ($yearChoice) {
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }

        if ($monthChoice && $monthChoice != 'all') {
            $stmt->andwhere('MONTH(e.dateStartAt) = :month');
            $stmt->setParameter('month', $monthChoice);
        }

        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('a.id = :activityChoice');
            $stmt->setParameter('activityChoice', $activityChoice);
        }

        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        return $stmt->getQuery();
    }

    public function getEventListforCalendar(string $yearChoice, string $activityChoice): array
    {
        $stmt = $this->createQueryBuilder('e');
        $stmt->join('e.activity', 'a');

        if ($yearChoice) {
            $stmt->andwhere('YEAR(e.dateStartAt) = :year');
            $stmt->setParameter('year', $yearChoice);
        }
        
        if ($activityChoice && $activityChoice != 'all') {
            $stmt->andwhere('a.id = :activityChoice');
            $stmt->setParameter('activityChoice', $activityChoice);
        }

        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        return $stmt->getQuery()->getResult();
    }

    public function getEventListforCalendarFor12Months(string $activityChoice, string $userChoice, string $yearChoice): array
    {

        // Création d'un objet QueryBuilder
        $stmt = $this->createQueryBuilder('e');
        $stmt->join('e.activity', 'a');
        
        if ($activityChoice && $activityChoice !== 'all') {
            $stmt->andWhere('a.id = :activityChoice')
                ->setParameter('activityChoice', $activityChoice);
        }

        if ($userChoice && $userChoice !== 'all') {
            $stmt->andWhere('e.user = :userChoice')
                ->setParameter('userChoice', $userChoice);
        }

        switch ($yearChoice) {
            case 'yearDepth':
                $yearDefault = (new \DateTime())->format('Y');
                $monthDefault = (new \DateTime())->format('m');
                $startDate = new \DateTime("{$yearDefault}-{$monthDefault}-01");
                $endDate = (clone $startDate)->modify('+12 months')->modify('-1 day'); // Dernier jour de la période de 12 mois
        
                // Filtrage par la plage de dates
                $stmt->andWhere('e.dateStartAt BETWEEN :startDate AND :endDate')
                    ->setParameter('startDate', $startDate)
                    ->setParameter('endDate', $endDate);
                break;
        
            default:
                $stmt->andWhere('YEAR(e.dateStartAt) = :year')
                    ->setParameter('year', $yearChoice);
                break;
        }
        
        // Tri des résultats par la date de début des événements
        $stmt->addOrderBy('e.dateStartAt', 'ASC');
        
        // Retourne les résultats
        return $stmt->getQuery()->getResult();
    }

    public function getDistincYearCreatedAt(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT DISTINCT(YEAR(date_start_at)) as year
                    FROM `event`
                    WHERE DATE(date_start_at) >= DATE(NOW())
                    ORDER BY year ASC
            ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistincYearCreatedAtForAgendaView(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT DISTINCT(YEAR(date_start_at)) as year
                    FROM `event`
                    WHERE DATE(date_start_at) >= DATE(NOW())
                    ORDER BY year DESC
               ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistincYearCreatedAtForArchiveView(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT DISTINCT(YEAR(date_start_at)) as year
                    FROM `event`
                    WHERE DATE(date_start_at) < DATE(NOW())
                    ORDER BY year DESC
               ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistinctMonthCreatedAt($yearChoice): array
    {
        $sql_yearChoice = "";
        if ($yearChoice != null && $yearChoice != 'all') {
            $sql_yearChoice = " AND YEAR(A.date_start_at) = :yearChoice ";
        }

        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT DISTINCT(MONTH(A.date_start_at)) AS month_number,
                    ELT(MONTH(A.date_start_at),
                        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre') AS month_name
                FROM `event` A
                WHERE id > 0
                " . $sql_yearChoice . "

                ORDER BY month_number";

        $stmt = $conn->prepare($sql);
        if ($yearChoice != null && $yearChoice != 'all') {
            $stmt->bindValue(':yearChoice', $yearChoice);
        }
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistinctMonthCreatedAtForAgendaView($yearChoice): array
    {
        $sql_yearChoice = "";
        if ($yearChoice != null && $yearChoice != 'all') {
            $sql_yearChoice = " AND YEAR(A.date_start_at) = :yearChoice ";
        }

        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT DISTINCT(MONTH(A.date_start_at)) AS month_number,
                    ELT(MONTH(A.date_start_at),
                        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre') AS month_name
                FROM `event` A
                WHERE DATE(date_start_at) >= DATE(NOW())
                " . $sql_yearChoice . "
                

                ORDER BY month_number";

        $stmt = $conn->prepare($sql);
        if ($yearChoice != null && $yearChoice != 'all') {
            $stmt->bindValue(':yearChoice', $yearChoice);
        }
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistinctMonthCreatedAtForArchiveView($yearChoice): array
    {
        $sql_yearChoice = "";
        if ($yearChoice != null) {
            $sql_yearChoice = " AND YEAR(A.date_start_at) = :yearChoice ";
        }

        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT DISTINCT(MONTH(A.date_start_at)) AS month_number,
                    ELT(MONTH(A.date_start_at),
                        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre') AS month_name
                FROM `event` A
                WHERE DATE(date_start_at) < DATE(NOW())
                " . $sql_yearChoice . "
                

                ORDER BY month_number";

        $stmt = $conn->prepare($sql);
        if ($yearChoice != null) {
            $stmt->bindValue(':yearChoice', $yearChoice);
        }
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistinctCreator(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT U.id AS user_id, CONCAT(UPPER(U.last_name), ' ', U.first_name) AS creator
                    FROM `event` E 
                    LEFT JOIN user U 
                    ON U.id = E.user_id

                    GROUP BY U.id
                    ORDER BY U.last_name ASC
               ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function getDistinctAnimator(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "    SELECT Ea.animator_id AS id, CONCAT(UPPER(A.last_name), ' ', A.first_name) AS name

                    FROM `event_animator` Ea 
                    LEFT JOIN animator A
                    ON A.id = Ea.animator_id

                    GROUP BY Ea.animator_id
                    ORDER BY A.last_name ASC
               ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }

    public function findNextUpcomingEvent(): mixed
    {
        $now = new \DateTime();
    
        return $this->createQueryBuilder('e')
            ->andWhere('e.isEnabled = :isEnabled') // Filtre pour événements actifs
            ->andWhere('(e.dateStartAt > :today OR (e.dateStartAt = :today AND e.timeStartAt >= :currentTime))') // Date et heure
            ->setParameter('isEnabled', true)
            ->setParameter('today', $now->format('Y-m-d')) // La date actuelle
            ->setParameter('currentTime', $now->format('H:i:s')) // L'heure actuelle
            ->orderBy('e.dateStartAt', 'ASC') // Trier d'abord par date
            ->addOrderBy('e.timeStartAt', 'ASC') // Puis par heure
            ->setMaxResults(1) // Limiter à un seul résultat
            ->getQuery()
            ->getOneOrNullResult(); // Retourner null si aucun résultat trouvé
    }

    public function findNextUpcomingList(): mixed
    {
        $now = new \DateTime();
    
        return $this->createQueryBuilder('e')
            ->andWhere('e.isEnabled = :isEnabled') // Filtre pour événements actifs
            ->andWhere('(e.dateStartAt > :today OR (e.dateStartAt = :today AND e.timeStartAt >= :currentTime))') // Date et heure
            ->setParameter('isEnabled', true)
            ->setParameter('today', $now->format('Y-m-d')) // La date actuelle
            ->setParameter('currentTime', $now->format('H:i:s')) // L'heure actuelle
            ->orderBy('e.dateStartAt', 'ASC') // Trier d'abord par date
            ->addOrderBy('e.timeStartAt', 'ASC') // Puis par heure
            ->setMaxResults(4) // Limiter à un seul résultat
            ->getQuery()
            ->getResult(); // Retourner null si aucun résultat trouvé
    }

    public function findLastPastEventsList(): mixed
    {
        $now = new \DateTime();
    
        return $this->createQueryBuilder('e')
            ->andWhere('e.isEnabled = :isEnabled') // Filtre pour événements actifs
            ->andWhere(
                '(e.dateEndAt < :today OR (e.dateEndAt = :today AND e.timeEndAt IS NOT NULL AND e.timeEndAt <= :currentTime))'
            ) // Exclure les événements avec timeEndAt=NULL aujourd'hui
            ->setParameter('isEnabled', true)
            ->setParameter('today', $now->format('Y-m-d')) // La date actuelle
            ->setParameter('currentTime', $now->format('H:i:s')) // L'heure actuelle
            ->orderBy('e.dateEndAt', 'ASC') // Trier par date de début
            ->setMaxResults(4) // Limiter à 4 résultats
            ->getQuery()
            ->getResult();
    }

    public function getAllEventsCount(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = " SELECT COUNT(e.id) as total FROM event e;
            ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery()->fetchAssociative();
        return $result;
    }

}
