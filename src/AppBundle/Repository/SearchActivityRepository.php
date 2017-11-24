<?php

namespace AppBundle\Repository;

/**
 * SearchActivityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SearchActivityRepository extends \Doctrine\ORM\EntityRepository
{
    public function findLast()
    {
        return $this->createQueryBuilder('sa')
            ->orderBy('sa.searchDate', 'ASC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleResult();
    }
}
