<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
    //query for users matching the given input
    public function searchForUsersQuery($input, $sort)
    {
        return $query = $this->createQueryBuilder('u')
            ->Where('u.username LIKE :input')
            ->orWhere('u.surname LIKE :input')
            ->orWhere('u.forename LIKE :input')
            ->setParameter('input', '%'.$input.'%')
            ->orderBy('u.'.$sort, 'DESC')
            ->getQuery();
    }
}
