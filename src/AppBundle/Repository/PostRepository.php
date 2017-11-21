<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    public function findPopularPosts($number)
    {
        $query = $this->createQueryBuilder('p')
          ->orderBy('p.views', 'DESC')
          ->getQuery();

        return $query->setMaxResults($number)->getResult();
    }

    public function findLastPosts($number)
    {
        $query = $this->createQueryBuilder('p')
          ->orderBy('p.createDate', 'DESC')
          ->getQuery();

        return $query->setMaxResults($number)->getResult();
    }

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

    public function findRelated(Post $post)
    {
        $entity = Tag::class;
        $tags = $post->getTags()->map(function($entity){
                return $entity->getId();
            })->toArray();
        $tags = implode(', ', $tags);
        $id = $post->getId();
        $title = $post->getTitle();
        $author = $post->getAuthor()->getId();

        return $query = $this->createQueryBuilder('p')
            ->select('p, sum(case when t.id in (:tags) then 1 else 0 end) as tags_num')
            ->leftJoin('p.tags', 't')
            ->where('t.id in (:tags) or p.author = :author or p.title like :title')
            ->andWhere('p.id != :id')
            ->setParameters([
                'tags' => $tags,
                'title' => '%'.$title.'%',
                'id' => $id,
                'author' => $author
            ])
            ->groupBy('p.id')
            ->orderBy('tags_num', 'DESC')
            ->getQuery()
            ->setMaxResults(4)
            ->getResult();
    }

    public function findByUserQuery(User $user)
    {
        return $query = $this->createQueryBuilder('p')
            ->where('p.author = :user')
            ->orderBy('p.createDate', 'DESC')
            ->setParameter('user', $user)
            ->getQuery();
    }

    public function findByFollowedUsers($followed)
    {
        return $this->createQueryBuilder('p')
            ->where('p.author IN (:followed)')
            ->orderBy('p.createDate', 'DESC')
            ->setParameter('followed', $followed)
            ->getQuery()
            ->setMaxResults(6)
            ->getResult();
    }
}
