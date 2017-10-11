<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

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
}
