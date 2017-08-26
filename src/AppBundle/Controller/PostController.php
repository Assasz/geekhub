<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Post;
use AppBundle\Form\PostType;

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
        $user = $this->getUser()->getUsername();

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
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
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

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/post/edit", name="post_edit")
     */
    public function editAction(Request $request)
    {
        return $this->render('post/edit.html.twig');
    }

    /**
     * @Route("/post/{id}", name="post", requirements={"id": "\d+"})
     */
    public function postAction(Request $request, $id)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);

        if (!$post)
        {
            throw $this->createNotFoundException('This post does not exist');
        }

        return $this->render('post/post.html.twig', [
            'post' => $post
        ]);
    }
}
