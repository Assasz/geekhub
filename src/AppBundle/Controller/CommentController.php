<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CommentType;
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
          ], ['createDate' => 'DESC']);

        return $this->render('comment/list.html.twig', [
          'comments' => $comments,
          'post' => $post
        ]);
    }

    /**
     * @Route("/comment/create/{post}/{parent}", name="comment_create", requirements={"post": "\d+", "parent": "\d+"})
     */
    public function createAction(Request $request, Post $post, Comment $parent = null)
    {
        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
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
