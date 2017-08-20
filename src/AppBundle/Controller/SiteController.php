<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SearchType;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

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
     * @Route("/search/{type}/{sortby}", name="search", requirements={"type": "posts|users"})
     */
    public function searchAction(Request $request, $type = 'posts', $sortby = 'createDate')
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

            $query = $postRepository->searchForPostsQuery($input, $sortby);

            $results['posts'] = $paginator->paginate(
              $query,
              $request->query->getInt('page', 1),
              $request->query->getInt('limit', 5)
            );
        }
        else
        {
            $userRepository = $this->getDoctrine()->getRepository(User::class);

            $query = $userRepository->searchForUsersQuery($input, $sortby);

            $results['users'] = $paginator->paginate(
              $query,
              $request->query->getInt('page', 1),
              $request->query->getInt('limit', 5)
            );
        }

        $results['for'] = $input;

        return $this->render('site/search_result.html.twig', [
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
