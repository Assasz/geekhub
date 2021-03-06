<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Entity\PostVoter;
use AppBundle\Entity\Tag;
use AppBundle\Entity\SearchActivity;
use AppBundle\Form\PostType;
use AppBundle\Service\FileUploader;
use AppBundle\Service\NotificationManager;

class PostController extends Controller
{
    public function popularAction()
    {
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findPopular();

        return $this->render('post/popular.html.twig', [
            'posts' => $posts
        ]);
    }

    public function latestAction()
    {
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findLatest();

        return $this->render('post/latest.html.twig', [
            'posts' => $posts
        ]);
    }

    public function recommendedAction()
    {
        $searchActivity = $this->getDoctrine()
            ->getRepository(SearchActivity::class)
            ->findByUser($this->getUser());

        $tags = [];

        foreach($searchActivity as $activity)
        {
            $tags[] = $activity->getTag()->getId();
        }

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findRecommended($tags);

        return $this->render('post/recommended.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/user/{user}/posts", name="user_post_list", requirements={"user": "\d+"})
     */
    public function userListAction(Request $request, User $user)
    {
        if(!$user)
        {
            throw $this->createNotFoundException('User does not exist');
        }

        $query = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findByUserQuery($user);

        $paginator = $this->get('knp_paginator');
        $posts = $paginator->paginate(
          $query,
          $request->query->getInt('page', 1),
          6
        );

        return $this->render('post/user_list.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/post/create", name="post_create")
     */
    public function createAction(Request $request, FileUploader $fileUploader, NotificationManager $notificationManager)
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
            $file = $post->getImage();
            $fileName = $fileUploader->upload($file);

            $post->setImage($fileName);
            $post->setAuthor($user);

            $tagsInput = mb_strtolower($form['tags']->getData());

            if(!empty($tagsInput))
            {
                $tags = explode(' ', $tagsInput);

                foreach ($tags as $tag)
                {
                    $duplicate = $this->getDoctrine()
                        ->getRepository(Tag::class)
                        ->findOneByName($tag);

                    if($duplicate)
                    {
                        $post->addTag($duplicate);
                    }
                    else
                    {
                        $newTag = new Tag();
                        $newTag->setName($tag);
                        $post->addTag($newTag);
                    }
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post added successfuly');

            $notificationManager->addNewPostNotification($post);
            $notificationManager->save();

            return $this->redirectToRoute('post', ['post' => $post->getId()]);
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

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->render('post/post.html.twig', [
            'post' => $post
        ]);
    }

    public function relatedAction(Request $request, Post $post)
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)
            ->findRelated($post);

        return $this->render('post/related.html.twig', [
            'posts' => $posts
        ]);
    }

    public function followedAction(Request $request)
    {
        $followedUsers = $this->getDoctrine()->getRepository(User::class)
             ->findFollowedUsers($this->getUser());

        $followed = [];

        foreach($followedUsers as $user)
        {
            $followed[] = $user->getId();
        }

        $posts = $this->getDoctrine()->getRepository(Post::class)
            ->findByFollowedUsers($followed);

        return $this->render('post/followed.html.twig', [
            'posts' => $posts
        ]);
    }
}
