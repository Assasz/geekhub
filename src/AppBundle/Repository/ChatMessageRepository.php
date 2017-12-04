<?php

namespace AppBundle\Repository;

/**
 * ChatMessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChatMessageRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByOffset($offset)
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.createDate', 'DESC')
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults(20)
            ->getResult();
    }
}
