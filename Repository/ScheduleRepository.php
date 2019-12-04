<?php

namespace Skrip42\Bundle\ChronBundle\Repository;

use Skrip42\Bundle\ChronBundle\Entity\Schedule;

use DateTime;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    /**
     * return all schedules
     *
     * @return array
     */
    public function getAll() : array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.active = 1')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * return part of schedules
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getPart(int $limit, int $offset) : array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int
     */
    public function getCount() : int
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
