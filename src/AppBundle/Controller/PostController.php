<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Post;
use AppBundle\Entity\PostVoter;
use AppBundle\Form\PostType;
use AppBundle\Form\RatingType;

class PostController extends Controller
{
    public function popularPostsAction($number)
    {
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findPopularPosts($number);

        return $this->render('post/popular_posts.html.twig', [
            'posts' => $posts
        ]);
    }

    public function lastPostsAction($number)
    {
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findLastPosts($number);

        return $this->render('post/last_posts.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/my-posts", name="user_post_list")
     */
    public function userListAction(Request $request)
    {
        $user = $this->getUser();

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)->findBy(
            ['author' => $user],
            ['createDate' => 'DESC']
          );

        return $this->render('post/user_list.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/post/create", name="post_create")
     */
    public function createAction(Request $request)
    {
        $securityChecker = $this->get('security.authorization_checker');

        if(!$securityChecker->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            throw $this->createAccessDeniedException();
        }

        $post = new Post;

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $post = $form->getData();
            $user = $this->getUser();

            $post->setAuthor($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post added successfuly');

            return $this->redirectToRoute('post', ['id' => $post->getId()]);
        }

        return $this->render('post/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/post/edit", name="post_edit")
     */
    public function editAction(Request $request)
    {
        return $this->render('post/edit.html.twig');
    }

    /**
     * @Route("/post/like/{post}",  name="post_like", options={"expose"=true}, requirements={"post": "\d+"})
     */
    public function likeAction(Request $request, Post $post)
    {
        $securityChecker = $this->get('security.authorization_checker');

        if(!$securityChecker->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        if(!$request->isXmlHttpRequest() || $user == $post->getAuthor())
        {
            throw new \Exception('This action is forbidden');
        }

        $voters = [];
        foreach ($post->getVoters() as $voter) {
            $voters[] = $voter->getUser();
        }

        if($user == $post->getAuthor() || in_array($user, $voters))
        {
            throw new \Exception('This action is forbidden');
        }

        $likes = $post->getLikes()+1;
        $post->setLikes($likes);

        $voter = new PostVoter();

        $voter->setPost($post);
        $voter->setUser($user);
        $voter->setChoice('like');

        $em = $this->getDoctrine()->getManager();
        $em->persist($voter);
        $em->flush();

        return new JsonResponse(['likes' => $likes]);

    }

    /**
     * @Route("/post/dislike/{post}",  name="post_dislike", options={"expose"=true}, requirements={"post": "\d+"})
     */
    public function dislikeAction(Request $request, Post $post)
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

        $voters = [];
        foreach ($post->getVoters() as $voter) {
            $voters[] = $voter->getUser();
        }

        if($user == $post->getAuthor() || in_array($user, $voters))
        {
            throw new \Exception('This action is forbidden');
        }

        $dislikes = $post->getDislikes()+1;
        $post->setDislikes($dislikes);

        $voter = new PostVoter();

        $voter->setPost($post);
        $voter->setUser($user);
        $voter->setChoice('dislike');

        $em = $this->getDoctrine()->getManager();
        $em->persist($voter);
        $em->flush();

        return new JsonResponse(['dislikes' => $dislikes]);
    }

    /**
     * @Route("/post/{post}", name="post", requirements={"post": "\d+"})
     */
    public function postAction(Request $request, Post $post)
    {
        if (!$post)
        {
            throw $this->createNotFoundException('This post does not exist');
        }

        $views = $post->getViews()+1;
        $post->setViews($views);

        return $this->render('post/post.html.twig', [
            'post' => $post
        ]);
    }
}
