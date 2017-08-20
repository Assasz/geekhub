<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
    //query for users matching the given input
    public function searchForUsersQuery($input, $sortby)
    {
        return $query = $this->createQueryBuilder('p')
          ->Where('p.username LIKE :input')
          ->orWhere('p.surname LIKE :input')
          ->orWhere('p.forename LIKE :input')
          ->setParameter('input', '%'.$input.'%')
          ->orderBy('p.'.$sortby, 'DESC')
          ->getQuery();
    }
}
