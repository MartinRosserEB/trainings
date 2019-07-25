<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrainingTypeRepository")
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
     * @ORM\OneToMany(targetEntity="TrainingTypePerson", mappedBy="trainingType", cascade={"persist"})
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
                return $trainingTypePerson->getActiveUntil() === null;
            }
        );
    }

    public function getActiveTrainingTypePersonFor(User $user)
    {
         $tTP = $this->trainingTypePersons->filter(
            function ($trainingTypePerson) use ($user) {
                return $trainingTypePerson->getActiveUntil() === null && $trainingTypePerson->getPerson()->getUser() === $user;
            }
        );
        if (count($tTP) > 0) {
            return $tTP->first();
        } else {
            return null;
        }
    }

    public function addTrainingTypePerson(TrainingTypePerson $trainingTypePerson)
    {
        $this->trainingTypePersons->add($trainingTypePerson);
        $trainingTypePerson->setTrainingType($this);

        return $this;
    }

    /**
     * Method only sets TrainingTypePerson inactive.
     *
     * @param \App\Entity\TrainingTypePerson $trainingTypePerson
     * @return $this
     */
    public function removeTrainingTypePerson(TrainingTypePerson $trainingTypePerson)
    {
        if ($this->trainingTypePersons->contains($trainingTypePerson)) {
            $trainingTypePerson->setActiveUntil(new \DateTime());
        }

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
