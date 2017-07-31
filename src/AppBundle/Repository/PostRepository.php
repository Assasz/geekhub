<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    // returns posts with the biggest number of views
    public function findPopularPosts($number)
    {
        $query = $this->createQueryBuilder('p')
          ->orderBy('p.views', 'ASC')
          ->getQuery();

        return $query->setMaxResults($number)->getResult();
    }

    // returns the newest posts
    public function findLastPosts($number)
    {
        $query = $this->createQueryBuilder('p')
          ->orderBy('p.createDate', 'DESC')
          ->getQuery();

        return $query->setMaxResults($number)->getResult();
    }

    //query for posts matching the given input
    public function searchForPosts($input, $sortby)
    {
        $tags = explode(" ", $input);

        $query = $this->createQueryBuilder('p')
          ->Where('p.title LIKE :input')
          ->orWhere('p.tags IN(:tags)')
          ->setParameter('input', '%'.$input.'%')
          ->setParameter('tags', $tags)
          ->orderBy('p.'.$sortby, 'DESC')
          ->getQuery();

        return $query->setMaxResults(10)->getResult();
    }
}
