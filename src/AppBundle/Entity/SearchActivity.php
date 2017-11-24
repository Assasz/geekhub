<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SearchActivity
 *
 * @ORM\Table(name="search_activity")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SearchActivityRepository")
 */
class SearchActivity
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="User", inversedBy="searchActivity")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Tag")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", nullable=false)
     */
    private $tag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="search_date", type="datetime")
     */
    private $searchDate;

    /**
     * @ORM\PrePersist
     */
    public function setSearchDateValue()
    {
        $this->searchDate = new \DateTime('now');
    }

    /**
     * Set searchDate
     *
     * @param \DateTime $searchDate
     *
     * @return SearchActivity
     */
    public function setSearchDate($searchDate)
    {
        $this->searchDate = $searchDate;

        return $this;
    }

    /**
     * Get searchDate
     *
     * @return \DateTime
     */
    public function getSearchDate()
    {
        return $this->searchDate;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return SearchActivity
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set tag
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return SearchActivity
     */
    public function setTag(\AppBundle\Entity\Tag $tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return \AppBundle\Entity\Tag
     */
    public function getTag()
    {
        return $this->tag;
    }
}
