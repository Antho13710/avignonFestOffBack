<?php

namespace App\Repository;

use App\Entity\Date;
use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Date|null find($id, $lockMode = null, $lockVersion = null)
 * @method Date|null findOneBy(array $criteria, array $orderBy = null)
 * @method Date[]    findAll()
 * @method Date[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Date::class);
    }

    /**
     * @return Date[] Returns an array of Date objects
     */
    public function deleteNull()
    {
        return $this->createQueryBuilder('date')
            ->delete()
            ->where('date.event is NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    
    public function findEventByDateAsc($limit)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin(Event::class, 'e')
            ->select('e')
            ->where('d.event = e.id')
            ->orderBy('d.startDate', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
    
}
