<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Form\SearchType;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;

class SiteController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        return $this->render('site/index.html.twig');
    }

    /**
     * @Route("/search/{type}/{sort}", name="search", requirements={"type": "posts|users"})
     */
    public function searchAction(Request $request, $type = 'posts', $sort = 'createDate')
    {
        $session = new Session();

        $input = $request->request->get('search_input') ?? $session->get('search_input');
        $session->set('search_input', $input);

        /**
        * @ var $paginator \Knp\Component\Pager\Paginator
        */
        $paginator = $this->get('knp_paginator');

        if($type == 'posts')
        {
            $postRepository = $this->getDoctrine()->getRepository(Post::class);

            $query = $postRepository->searchForPostsQuery($input, $sort);

            $results['posts'] = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                6
            );
        }
        else
        {
            $userRepository = $this->getDoctrine()->getRepository(User::class);

            $query = $userRepository->searchForUsersQuery($input, $sort);

            $results['users'] = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                6
            );
        }

        $results['for'] = $input;

        return $this->render('site/search.html.twig', [
            'results' => $results
        ]);
    }

    /**
     * @Route("/terms-of-service", name="terms_of_service")
     */
    public function termsOfServiceAction(Request $request)
    {
        return $this->render('site/terms.html.twig');
    }

    /**
     * @Route("/privacy-policy", name="privacy_policy")
     */
    public function privacyPolicyAction(Request $request)
    {
        return $this->render('site/privacy.html.twig');
    }
}
