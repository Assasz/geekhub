<?php
namespace AppBundle\EntityListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\Post;

class PostListener
{
    public function postLoad(Post $post, LifecycleEventArgs $event)
    {
        $entityManager = $event->getObjectManager();
        $likes = $post->getLikes();
        $dislikes = $post->getDislikes();

        $post->setRating($likes-$dislikes);
        $entityManager->flush();
    }
}
