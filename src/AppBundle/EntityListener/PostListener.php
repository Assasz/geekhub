<?php
namespace AppBundle\EntityListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\Post;

class PostListener
{
    public function postLoad(Post $post, LifecycleEventArgs $event)
    {
        $entityManager = $event->getObjectManager();
        $totalRating = $post->getTotalRating();
        $votes = $post->getVotes();

        if($votes > 0)
        {
          $rating = $totalRating / $votes;
        }
        else 
        {
          $rating = 0;
        }

        $post->setRating($rating);
        $entityManager->flush();
    }
}
