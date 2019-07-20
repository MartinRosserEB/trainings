<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"public"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"public"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"public"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"public"})
     */
    private $familyName;

    /**
     * @ORM\OneToMany(targetEntity="TrainingTypeUser", mappedBy="user")
     */
    private $userTrainingTypes;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $apiToken;

    public function __construct()
    {
        $this->userTrainingTypes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        $name = '';
        if ($this->firstName) {
            $name .= $this->firstName;
        }
        if ($this->familyName) {
            if ($name !== '') {
                $name .= ' ';
            }
            $name .= $this->familyName;
        }
        if ($name === '') {
            $name = $this->getUser();
        }
        return (string) $name;
    }

    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getUserTrainingTypes()
    {
        return $this->userTrainingTypes;
    }

    public function addUserTrainingType(TrainingTypeUser $userTrainingType)
    {
        $this->userTrainingTypes->add($userTrainingType);
    }

    public function removeUserTrainingType(TrainingTypeUser $userTrainingType)
    {
        $this->userTrainingTypes->remove($userTrainingType);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getApiToken() : string
    {
        return (string) $this->apiToken;
    }

    public function setApiToken(string $token)
    {
        $this->apiToken = $token;
    }

    public function __toString()
    {
        return $this->getUsername();
    }
}
