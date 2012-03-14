<?php

namespace CheckTimer\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * CheckTimer\UserBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="CheckTimer\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(fields="username", message="Lo username inserito è già stato usato.")
 * @UniqueEntity(fields="email", message="L'email inserita è già stata usata.")
 * @UniqueEntity(fields="new_email", message="L'email inserita è già stata usata.")
 */
class User implements AdvancedUserInterface
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
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="Non hai inserito il tuo indirizzo email.", groups={"Registration"})
     * @Assert\Email(message="Non hai inserito un indirizzo email valido.", groups={"Registration"})
     */
    protected $email;

    /**
     * @var string $new_email
     *
     * @ORM\Column(name="new_email", type="string", length=255, unique=true, nullable=true)
     *
     * @Assert\Email(message="Non hai inserito un indirizzo email valido.", groups={"Profile"})
     */
    protected $new_email;

    /**
     * @var datetime $email_changed_at
     *
     * @ORM\Column(name="email_changed_at", type="datetime", nullable=true)
     */
    protected $email_changed_at;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="Non hai inserito il tuo username.", groups={"Registration"})
     * @Assert\MinLength(limit="3", message="Lo username dev'essere lungo almeno {{ limit }} caratteri.", groups={"Registration"})
     * @Assert\MaxLength(limit="255", message="Lo username può essere lungo al massimo {{ limit }} caratteri.", groups={"Registration"})
     * @Assert\Regex(pattern="/^[a-zA-Z0-9-\._]+$/", message="Lo username non è valido.")
     */
    protected $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     *
     * @Assert\NotBlank(message="Non hai inserito la tua password.", groups={"Registration"})
     * @Assert\MinLength(limit="8", message="La password dev'essere lunga almeno {{ limit }} caratteri.")
     * @Assert\MaxLength(limit="255", message="La password può essere lunga al massimo {{ limit }} caratteri.")
     */
    protected $password;

    /**
     * @var string $password_reset_code
     *
     * @ORM\Column(name="password_reset_code", type="string", length=255, nullable=true)
     */
    protected $password_reset_code;

    /**
     * @var datetime $password_reset_at
     *
     * @ORM\Column(name="password_reset_at", type="datetime", nullable=true)
     */
    protected $password_reset_at;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string", length=23)
     */
    protected $code;

    /**
     * @var array $roles
     *
     * @ORM\Column(name="roles", type="array")
     */
    protected $roles;

    /**
     * @var boolean $enabled
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @var datetime $registered_at
     *
     * @ORM\Column(name="registered_at", type="datetime")
     */
    protected $registered_at;

    /**
     * @var boolean $locked
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    protected $locked;

    /**
     * @ORM\OneToMany(targetEntity="CheckTimer\GameBundle\Entity\Highscore", mappedBy="user", cascade={"remove"})
     */
    protected $highscores;

    static public function makeSalt()
    {
        return sha1(uniqid(mt_rand()));
    }

    static public function makeCode()
    {
        return substr(self::makeSalt(), 0, 23);
    }

    public function __construct()
    {
        $this->salt    = self::makeSalt();
        $this->code    = self::makeCode();
        $this->enabled = false;
        $this->locked  = false;
        $this->roles   = array('ROLE_USER');
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->registered_at = new \DateTime();
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function equals(UserInterface $user)
    {
        if ($user instanceof User) {
            return ($user->getId() === $this->getId());
        }

        return ($user->getUsername() === $this->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked()
    {
        return !($this->getLocked());
    }

    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {
        return $this->getEnabled();
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
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set new_email
     *
     * @param string $newEmail
     */
    public function setNewEmail($newEmail)
    {
        $this->new_email = $newEmail;
    }

    /**
     * Get new_email
     *
     * @return string
     */
    public function getNewEmail()
    {
        return $this->new_email;
    }

    /**
     * Set email_changed_at
     *
     * @param datetime $emailChangedAt
     */
    public function setEmailChangedAt($emailChangedAt)
    {
        $this->email_changed_at = $emailChangedAt;
    }

    /**
     * Get email_changed_at
     *
     * @return datetime
     */
    public function getEmailChangedAt()
    {
        return $this->email_changed_at;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password_reset_code
     *
     * @param string $passwordResetCode
     */
    public function setPasswordResetCode($passwordResetCode)
    {
        $this->password_reset_code = $passwordResetCode;
    }

    /**
     * Get password_reset_code
     *
     * @return string
     */
    public function getPasswordResetCode()
    {
        return $this->password_reset_code;
    }

    /**
     * Set password_reset_at
     *
     * @param datetime $passwordResetAt
     */
    public function setPasswordResetAt($passwordResetAt)
    {
        $this->password_reset_at = $passwordResetAt;
    }

    /**
     * Get password_reset_at
     *
     * @return datetime
     */
    public function getPasswordResetAt()
    {
        return $this->password_reset_at;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set roles
     *
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set registered_at
     *
     * @param datetime $registeredAt
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registered_at = $registeredAt;
    }

    /**
     * Get registered_at
     *
     * @return datetime
     */
    public function getRegisteredAt()
    {
        return $this->registered_at;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
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
