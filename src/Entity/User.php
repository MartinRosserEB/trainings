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
     * @ORM\Column(type="string", length=180)
     * @Groups({"public"})
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="user", cascade={"persist"})
     */
    private $persons;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @var string The hashed password
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $apiToken;

    public function __construct()
    {
        $this->persons = new ArrayCollection();
        $this->roles = [];
    }

    public function getId(): ?int
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function addRole(string $role)
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role)
    {
        if ($this->roles !== null) {
            $index = array_search($role, $this->roles);
            if ($index !== false) {
                array_splice($this->roles, $index, 1);
            }
        }

        return $this;
    }

    public function getPersons()
    {
        return $this->persons;
    }

    public function addPerson(Person $person)
    {
        $person->setUser($this);
        $this->persons->add($person);
        return $this;
    }

    public function removePerson(Person $person)
    {
        if ($this->persons->contains($person)) {
            $person->setUser(null);
            $this->persons->removeElement($person);
        }
        return $this;
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
    }

    public function getApiToken() : string
    {
        return (string) $this->apiToken;
    }

    public function setApiToken(string $token)
    {
        $this->apiToken = $token;

        return $this;
    }

    public function __toString()
    {
        return $this->getUsername();
    }
}
