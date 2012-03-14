<?php

namespace CheckTimer\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckTimer\GameBundle\Entity\Level
 *
 * @ORM\Table(name="levels")
 * @ORM\Entity(repositoryClass="CheckTimer\GameBundle\Entity\LevelRepository")
 */
class Level
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
     * @var float $min_time
     *
     * @ORM\Column(name="min_time", type="float")
     */
    protected $min_time;

    /**
     * @var float $max_time
     *
     * @ORM\Column(name="max_time", type="float")
     */
    protected $max_time;

    /**
     * @ORM\OneToMany(targetEntity="Highscore", mappedBy="level", cascade={"remove"})
     */
    protected $highscores;
    public function __construct()
    {
        $this->highscores = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set min_time
     *
     * @param float $minTime
     */
    public function setMinTime($minTime)
    {
        $this->min_time = $minTime;
    }

    /**
     * Get min_time
     *
     * @return float
     */
    public function getMinTime()
    {
        return $this->min_time;
    }

    /**
     * Set max_time
     *
     * @param float $maxTime
     */
    public function setMaxTime($maxTime)
    {
        $this->max_time = $maxTime;
    }

    /**
     * Get max_time
     *
     * @return float
     */
    public function getMaxTime()
    {
        return $this->max_time;
    }

    /**
     * Add highscores
     *
     * @param CheckTimer\GameBundle\Entity\Highscore $highscores
     */
    public function addHighscore(\CheckTimer\GameBundle\Entity\Highscore $highscores)
    {
        $this->highscores[] = $highscores;
    }

    /**
     * Get highscores
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getHighscores()
    {
        return $this->highscores;
    }
}
