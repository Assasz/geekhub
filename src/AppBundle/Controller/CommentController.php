<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CommentType;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Entity\Comment;

class CommentController extends Controller
{
    public function listAction(Post $post)
    {
        $comments = $this->getDoctrine()->getRepository(Comment::class)
          ->findBy([
              'post' => $post,
              'parent' => null
            ],
            ['createDate' => 'DESC']
          );

        return $this->render('comment/list.html.twig', [
          'comments' => $comments
        ]);
    }

    public function listRepliesAction(Comment $parent)
    {
        $replies = $this->getDoctrine()->getRepository(Comment::class)
          ->findBy(
            ['parent' => $parent],
            ['createDate' => 'ASC']
          );

        return $this->render('comment/list_replies.html.twig', [
          'comments' => $replies,
          'parent' => $parent
        ]);
    }

    /**
     * @Route("/comment/vote/{comment}",  name="comment_vote", options={"expose"=true}, requirements={"comment": "\d+"})
     */
     public function voteAction(Request $request, Comment $comment)
     {
         if($request->isXmlHttpRequest())
         {
           $votes = $comment->getVotes()+1;;
           $comment->setVotes($votes);
           $comment->addVoter($this->getUser());

           $em = $this->getDoctrine()->getManager();
           $em->flush();

           return new JsonResponse(['votes' => $votes]);
         }

         throw new \Exception('This action is forbidden');
     }

    /**
     * @Route("/comment/create/{post}/{parent}",  name="comment_create", requirements={"post": "\d+", "parent": "\d+"})
     */
    public function createAction(Request $request, Post $post, $parent = null)
    {
        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
          if(!$form->isValid())
          {
            $this->addFlash('danger', 'Did you try to post empty comment?');

            return $this->redirectToRoute('post', ['id' => $post->getId()]);
          }

          $comment = $form->getData();
          $user = $this->getUser();

          $comment->setAuthor($user);
          $comment->setPost($post);
          $comment->setParent($parent);

          $em = $this->getDoctrine()->getManager();
          $em->persist($comment);
          $em->flush();

          $this->addFlash('success', 'Comment added successfuly');

          return $this->redirectToRoute('post', ['id' => $post->getId()]);
        }

        return $this->render('comment/create.html.twig', [
          'form' => $form->createView(),
          'post' => $post
        ]);
    }
}
