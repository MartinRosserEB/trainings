<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 */
class TrainingType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     * @Groups({"public"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Groups({"public"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="TrainingTypePerson", mappedBy="trainingType")
     */
    private $trainingTypePersons;

    /**
     * @ORM\OneToMany(targetEntity="Training", mappedBy="trainingType")
     */
    private $trainings;

    public function __construct()
    {
        $this->trainingTypePersons = new ArrayCollection();
        $this->trainings = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getTrainingTypePersons()
    {
        return $this->trainingTypePersons;
    }

    public function getActiveTrainingTypePersons()
    {
        return $this->trainingTypePersons->filter(
            function ($trainingTypePerson) {
                return $trainingTypePerson->getActiveUntil() === null ;
            }
        );
    }

    public function addTrainingTypePerson(TrainingTypePerson $trainingTypePerson)
    {
        $this->trainingTypePersons->add($trainingTypePerson);

        return $this;
    }

    public function removeTrainingTypePerson(TrainingTypePerson $trainingTypePerson)
    {
        $this->trainingTypePersons->remove($trainingTypePerson);

        return $this;
    }

    public function getTrainings()
    {
        return $this->trainings;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
