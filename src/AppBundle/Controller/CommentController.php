<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\CommentType;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Entity\Comment;

class CommentController extends Controller
{
    /**
     * @Route("/comment/list/{post}",  name="comment_list", options={"expose"=true}, requirements={"post": "\d+"})
     */
    public function listAction(Request $request, Post $post)
    {
        $repository = $this->getDoctrine()->getRepository(Comment::class);

        $query = $repository->listCommentsQuery($post);

        /**
        * @ var $paginator \Knp\Component\Pager\Paginator
        */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,
            ['defaultSortFieldName' => ['c.votes', 'c.createDate'], 'defaultSortDirection' => 'desc']
          );

        return $this->render('comment/list.html.twig', [
            'comments' => $result,
            'post' => $post,
            'request' => $request
        ]);
    }

    /**
     * @Route("/comment/list-replies/{parent}/{show}",  name="comment_list_replies", options={"expose"=true}, requirements={"parent": "\d+"})
     */
    public function listRepliesAction(Request $request, Comment $parent, $show = false)
    {
        $repository = $this->getDoctrine()->getRepository(Comment::class);

        if($show && $request->isXmlHttpRequest())
        {
            $replies = $repository->findBy(
                ['parent' => $parent],
                ['createDate' => 'ASC']
            );

            $repliesNumber = $repository->countReplies($parent);

            $response = $this->renderView('comment/list_replies.html.twig', [
                'comments' => $replies,
                'repliesNumber' => $repliesNumber
            ]);

            return new JsonResponse(['replies' => $response]);
        }

        $repliesNumber = $repository->countReplies($parent);

        return $this->render('comment/list_replies.html.twig', [
            'repliesNumber' => $repliesNumber,
            'parent' => $parent
        ]);
    }

    /**
     * @Route("/comment/vote/{comment}",  name="comment_vote", options={"expose"=true}, requirements={"comment": "\d+"})
     */
     public function voteAction(Request $request, Comment $comment)
     {
         $securityChecker = $this->get('security.authorization_checker');

         if(!$securityChecker->isGranted('IS_AUTHENTICATED_REMEMBERED'))
         {
             throw $this->createAccessDeniedException();
         }

         if(!$request->isXmlHttpRequest())
         {
             throw new \Exception('This action is forbidden');
         }

         $user = $this->getUser();

         if($user == $comment->getAuthor() || in_array($user, $comment->getVoters()->toArray()))
         {
             throw new \Exception('This action is forbidden');
         }

         $votes = $comment->getVotes()+1;
         $comment->setVotes($votes);
         $comment->addVoter($user);

         $em = $this->getDoctrine()->getManager();
         $em->flush();

         return new JsonResponse(['votes' => $votes]);
     }

    /**
     * @Route("/comment/create/{post}/{parent}",  name="comment_create", requirements={"post": "\d+", "parent": "\d+"})
     */
    public function createAction(Request $request, Post $post, $parent = null)
    {
        $securityChecker = $this->get('security.authorization_checker');

        if(!$securityChecker->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            throw $this->createAccessDeniedException();
        }

        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() &&  $request->isXmlHttpRequest())
        {
            $comment = $form->getData();
            $user = $this->getUser();

            $comment->setAuthor($user);
            $comment->setPost($post);
            $comment->setParent($parent);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $response = $this->renderView('comment/content.html.twig', [
                'comment' => $comment
            ]);

            return new JsonResponse([
                'comment' => $response,
                'parent' => $parent
            ]);
        }

        return $this->render('comment/create.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }
}
