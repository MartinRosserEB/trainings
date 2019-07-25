<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class TrainingTypePerson
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="personTrainingTypes")
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity="TrainingType", inversedBy="trainingTypePersons")
     */
    private $trainingType;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $role;

    /**
     * @ORM\Column(type="datetime")
     */
    private $activeSince;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activeUntil;

    public function getPerson()
    {
        return $this->person;
    }

    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    public function getTrainingType()
    {
        return $this->trainingType;
    }

    public function setTrainingType(?TrainingType $trainingType)
    {
        $this->trainingType = $trainingType;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole(String $role)
    {
        $this->role = $role;

        return $this;
    }

    public function getActiveSince()
    {
        return $this->activeSince;
    }

    public function setActiveSince(\DateTime $activeSince)
    {
        $this->activeSince = $activeSince;

        return $this;
    }

    public function getActiveUntil()
    {
        return $this->activeUntil;
    }

    public function setActiveUntil(\DateTime $activeUntil)
    {
        $this->activeUntil = $activeUntil;

        return $this;
    }

    public function __toString() {
        return $this->getPerson()->__toString();
    }

    public function getId(): int
    {
        return $this->id;
    }
}
