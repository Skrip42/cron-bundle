<?php

namespace Skrip42\Bundle\CronBundle\Repository;

use Skrip42\Bundle\CronBundle\Entity\Schedule;

use DateTime;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Skrip42\AdvancedRepository\AdvancedRepository;

/**
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends AdvancedRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    /**
     * return all schedules
     *
     * @param bool $all = false get disabled schedule
     *
     * @return array
     */
    public function getAll(bool $all = false) : array
    {
        $builder = $this->createQueryBuilder('s');
        if (!$all) {
            $builder->andWhere('s.active=1');
        }
        $builder->orderBy('s.id', 'DESC');
        return $builder->getQuery()->getResult();
    }

    /**
     * return part of schedules
     *
     * @param int $limit
     * @param int $offset
     * @param bool $all = true // get disabled schedule
     *
     * @return array
     */
    public function getPart(int $limit, int $offset, bool $all = true) : array
    {
        $builder =  $this->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        if (!$all) {
            $builder->andWhere('s.active=1');
        }
        return $builder->getQuery()->getResult();
    }

    /**
     * @param bool $all = false // include disabled schedule
     *
     * @return int
     */
    public function getCount(bool $all = true) : int
    {
        $builder = $this->createQueryBuilder('s')
                        ->select('count(s.id)');
        if (!$all) {
            $builder->andWhere('s.active=1');
        }
        return $builder->getQuery()->getSingleScalarResult();
    }
}
