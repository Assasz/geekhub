<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Tag;

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
}
