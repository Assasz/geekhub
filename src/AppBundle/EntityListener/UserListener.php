<?php
namespace AppBundle\EntityListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\User;
use AppBundle\Entity\Rank;

class UserListener
{
    public function postLoad(User $user, LifecycleEventArgs $event)
    {
        $entityManager = $event->getObjectManager();
        $reputation = $user->getReputation();

        $rank = $entityManager->getRepository(Rank::class)
        ->findUserRank($reputation);
        $user->setRank($rank);

        $entityManager->flush();
    }
}
