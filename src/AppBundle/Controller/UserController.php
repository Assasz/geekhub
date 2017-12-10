<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\User;
use AppBundle\Service\NotificationManager;

class UserController extends Controller
{
    /**
     * @Route("/user/{user}", name="user", requirements={"user": "\d+"})
     */
    public function profileAction(Request $request, User $user)
    {
        if(!$user)
        {
            throw $this->createNotFoundException('This user does not exist');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/follow/{user}", name="user_follow", requirements={"user": "\d+"}, options={"expose"=true})
     */
    public function followAction(Request $request, User $user, NotificationManager $notificationManager)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception("This action is forbidden");
        }

        $followers = $user->getFollowers()->toArray();
        $followersIds = [];

        foreach($followers as $follower)
        {
            $followersIds[] = $follower->getId();
        }

        if(!in_array($this->getUser()->getId(), $followersIds))
        {
            $user->addFollower($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $notificationManager->addNewFollowerNotification($user);
            $notificationManager->save();
        }

        return new JsonResponse();
    }
}
