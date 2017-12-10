<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use AppBundle\Entity\User;

class NotificationManager
{
    private $em;
    private $notifications;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->notifications = [];
    }

    public function addNewPostNotification(Post $post)
    {
        $triggeringUser = $post->getAuthor();
        $followers = $triggeringUser->getFollowers()->toArray();

        foreach ($followers as $follower)
        {
            $args = [
                'type' => 'new_post',
                'targetUser' => $follower,
                'triggeringUser' => $triggeringUser,
                'object' => $post
            ];

            $this->notifications[] = $this->createNotification($args);
        }
    }

    public function addNewCommentNotification(Comment $comment)
    {
        $targetUser = $comment->getPost()->getAuthor();
        $triggeringUser = $comment->getAuthor();

        $args = [
            'type' => 'new_comment',
            'targetUser' => $targetUser,
            'triggeringUser' => $triggeringUser,
            'object' => $comment
        ];

        $this->notifications[] = $this->createNotification($args);
    }

    public function addNewReplyNotification(Comment $comment)
    {
        $targets = $comment->getParent()->getReplies()->toArray();
        $targets[] = $comment->getParent();
        $triggeringUser = $comment->getAuthor();

        $targetUsers = [];

        foreach($targets as $target)
        {
            if(!in_array($target->getAuthor(), $targetUsers) && $comment->getAuthor() != $target->getAuthor())
            {
                $targetUsers[] = $target->getAuthor();
            }
        }

        foreach ($targetUsers as $targetUser)
        {
            $args = [
                'type' => 'new_reply',
                'targetUser' => $targetUser,
                'triggeringUser' => $triggeringUser,
                'object' => $comment
            ];

            $this->notifications[] = $this->createNotification($args);
        }
    }

    public function addNewFollowerNotification(User $user)
    {
        $followers = $user->getFollowers()->toArray();
        $triggeringUser = end($followers);

        $args = [
            'type' => 'new_follower',
            'targetUser' => $user,
            'triggeringUser' => $triggeringUser
        ];

        $this->notifications[] = $this->createNotification($args);
    }

    public function createNotification(array $args)
    {
        $notification = new Notification();

        $notification->setType($args['type']);
        $notification->setTargetUser($args['targetUser']);
        $notification->setTriggeringUser($args['triggeringUser']);

        if(!empty($args['object']))
        {
            if($args['object'] instanceof Post)
            {
                $notification->setPost($args['object']);
            }

            if($args['object'] instanceof Comment)
            {
                $notification->setComment($args['object']);
            }
        }

        return $notification;
    }

    public function save()
    {
        foreach ($this->notifications as $notification)
        {
            $this->em->persist($notification);
        }

        $this->em->flush();
    }
}
