<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\User;
use AppBundle\Entity\Post;

/**
 * PostVoter
 *
 * @ORM\Table(name="post_voter")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostVoterRepository")
 */
class PostVoter
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="voters")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
     */
    private $post;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", columnDefinition="ENUM('like', 'dislike')")
     */
    private $choice;

    /**
     * Set post
     *
     * @param Post $post
     *
     * @return PostVoter
     */
    public function setPost(Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get postId
     *
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return PostVoter
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get userId
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set choice
     *
     * @param string $choice
     *
     * @return PostVoter
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;

        return $this;
    }

    /**
     * Get choice
     *
     * @return string
     */
    public function getChoice()
    {
        return $this->choice;
    }
}
