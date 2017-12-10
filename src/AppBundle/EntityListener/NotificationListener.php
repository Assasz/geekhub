<?php
namespace AppBundle\EntityListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\Notification;

class NotificationListener
{
    public function prePersist(Notification $notification, LifecycleEventArgs $event)
    {
        $em = $event->getObjectManager();

        $user = $notification->getTargetUser();
        $userNotifications = $em->getRepository(Notification::class)
            ->findBy(['targetUser' => $user], ['date' => 'ASC']);

        if(count($userNotifications) > 49)
        {
            $oldestNotification = end($userNotifications);

            $user->removeNotification($oldestNotification);
            $em->remove($oldestNotification);
            $em->flush();
        }
    }
}
