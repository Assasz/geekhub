<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\Entity\User;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
    //query for users matching the given input
    public function searchForUsersQuery($input, $sort)
    {
        $query = $this->createQueryBuilder('u')
            ->Where('u.username LIKE :input')
            ->orWhere('u.surname LIKE :input')
            ->orWhere('u.forename LIKE :input')
            ->setParameter('input', '%'.$input.'%')
            ->leftJoin('u.posts', 'p')
            ->leftJoin('u.followers', 'f')
            ->select('u, count(p.id) as posts, count(f.id) as followers')
            ->groupBy('u.id');

        if($sort == 'createDate')
        {
            $query->orderBy('u.createDate', 'ASC');
        }
        else
        {
            $query->orderBy($sort, 'DESC');
        }

        return $query->getQuery();
    }

    public function findFollowedUsers(User $user)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.followers', 'f')
            ->where('f.id = :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getResult();
    }
}
