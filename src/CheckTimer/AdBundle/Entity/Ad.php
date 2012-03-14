<?php

namespace CheckTimer\AdBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckTimer\AdBundle\Entity\Ad
 *
 * @ORM\Table(name="ads")
 * @ORM\Entity(repositoryClass="CheckTimer\AdBundle\Entity\AdRepository")
 */
class Ad
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var text $code
     *
     * @ORM\Column(name="code", type="text")
     */
    protected $code;

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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param text $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return text
     */
    public function getCode()
    {
        return $this->code;
    }
}
