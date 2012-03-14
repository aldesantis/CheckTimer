<?php

namespace CheckTimer\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckTimer\GameBundle\Entity\Highscore
 *
 * @ORM\Table(name="highscores")
 * @ORM\Entity(repositoryClass="CheckTimer\GameBundle\Entity\HighscoreRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Highscore
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var float $time
     *
     * @ORM\Column(name="time", type="float")
     */
    protected $time;

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="Level")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id")
     */
    protected $level;

    /**
     * @ORM\ManyToOne(targetEntity="CheckTimer\UserBundle\Entity\User", inversedBy="highscores")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set time
     *
     * @param float $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * Get time
     *
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set level
     *
     * @param CheckTimer\GameBundle\Entity\Level $level
     */
    public function setLevel(\CheckTimer\GameBundle\Entity\Level $level)
    {
        $this->level = $level;
    }

    /**
     * Get level
     *
     * @return CheckTimer\GameBundle\Entity\Level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set user
     *
     * @param CheckTimer\UserBundle\Entity\User $user
     */
    public function setUser(\CheckTimer\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return CheckTimer\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
