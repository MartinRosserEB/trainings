<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 */
class Person
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
    private $firstName;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"public"})
     */
    private $familyName;

    /**
     * @ORM\Column(name="birthdate", type="datetime", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $street;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $streetNo;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $phone;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="persons")
     * @Groups({"public"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="TrainingTypePerson", mappedBy="person")
     */
    private $personTrainingTypes;

    public function __construct()
    {
        $this->personTrainingTypes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function setStreetNo($streetNo)
    {
        $this->streetNo = $streetNo;

        return $this;
    }

    public function getStreetNo()
    {
        return $this->streetNo;
    }

    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getZipCode()
    {
        return $this->zipCode;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getPersonTrainingTypes()
    {
        return $this->personTrainingTypes;
    }

    public function addPersonTrainingType(TrainingTypePerson $personTrainingType)
    {
        $this->personTrainingTypes->add($personTrainingType);
    }

    public function removePersonTrainingType(TrainingTypePerson $personTrainingType)
    {
        $this->personTrainingTypes->remove($personTrainingType);
    }

    public function __toString()
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
        return $name;
    }
}
