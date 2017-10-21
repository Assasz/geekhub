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
          ->orderBy('p.views', 'DESC')
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
    public function searchForPostsQuery($input, $sort)
    {
        $tags = explode(" ", $input);

        return $query = $this->createQueryBuilder('p')
          ->Where('p.title LIKE :input')
          ->leftJoin('p.tags', 't')
          ->orWhere('t.name IN(:tags)')
          ->setParameter('input', '%'.$input.'%')
          ->setParameter('tags', $tags)
          ->orderBy('p.'.$sort, 'DESC')
          ->getQuery();
    }

    public function searchByTagQuery($tag, $sort)
    {
        return $query = $this->createQueryBuilder('p')
          ->leftJoin('p.tags', 't')
          ->Where('t.name = :tag')
          ->setParameter('tag', $tag)
          ->orderBy('p.'.$sort, 'DESC')
          ->getQuery();
    }
}
