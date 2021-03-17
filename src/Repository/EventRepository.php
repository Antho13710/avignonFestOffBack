<?php

namespace App\Repository;

use App\Entity\Alert;
use App\Entity\Date;
use App\Entity\DayOff;
use App\Entity\Event;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
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

    public function findWithLimit($limit)
    {
        return $this->createQueryBuilder('event')
            ->setMaxResults($limit)
            ->orderBy('date')
            ->getQuery()
            ->getResult()
        ;
    }

    public function filter($date, $type, $place, $price, $word, $limit = null)
    {
        $qb = $this->createQueryBuilder('e')
                ->select('e');

        if ($date != null) {
            $qb->innerJoin(Date::class, 'd')
                ->where('d.event = e')
                ->andwhere('d.startDate <= :date')
                ->andWhere('d.endDate >= :date')
                ->innerJoin(DayOff::class, 'do')
                ->andWhere('do.event = e')
                ->andWhere('do.date != :date')
                ->setParameter('date', new \DateTime($date));
        }
        if ($type != null) {
            $qb->andWhere('e.type = :type')
                ->setParameter('type', $type);
        }
        if ($place != null) {
            $qb->andWhere('e.place = :place')
                ->setParameter('place', $place);
        }
        if ($price != null) {
            $qb->andWhere('e.fullPrice < :price')
                ->setParameter('price', $price);
        }
        if ($word != null) {
             $qb->andWhere('e.title LIKE :word')
                ->setParameter('word', '%'.$word.'%');
         }

        return $qb->setMaxResults($limit)->getQuery()->getResult();
    }

    // Query for Home Admin

    public function findForHomeAdmin()
    {
        return $this->createQueryBuilder('event')
            ->innerJoin(Alert::class, 'alert')
            ->select('event.id, event.title, count(alert.event) as count_alert')
            ->where('event.id = alert.event')
            ->groupBy('alert.event')
            ->orderBy('count_alert', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
