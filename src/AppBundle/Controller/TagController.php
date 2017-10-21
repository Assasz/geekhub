<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Post;

class TagController extends Controller
{
    /**
     * @Route("/tag/list", name="tag_list", options={"expose"=true})
     */
    public function listAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception('This action is forbidden');
        }

        $tags = $this->getDoctrine()
            ->getRepository(Tag::class)
            ->findAll();

        $response = [];
        foreach ($tags as $tag)
        {
            $response[] = $tag->getName();
        }

        return new JsonResponse(['tags' => $response]);
    }

    /**
     * @Route("/tag/search/{tag}/{sort}", name="tag_search", requirements={"tag": "\d+", "sort": "createDate|rating|views"})
     */
    public function searchAction(Request $request, Tag $tag, $sort = 'createDate')
    {
        $paginator = $this->get('knp_paginator');

        $postRepository = $this->getDoctrine()->getRepository(Post::class);

        $query = $postRepository->searchByTagQuery($tag->getName(), $sort);

        $posts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('tag/search.html.twig', [
            'posts' => $posts,
            'tag' => $tag
        ]);
    }
}
