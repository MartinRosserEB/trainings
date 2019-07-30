<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @author mrosser
 * @ORM\Entity()
 * @UniqueEntity(fields={"person", "training"})
 */
class Attendance {
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Training", inversedBy="attendances")
     */
    private $training;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $enlistingIp;

    /**
     * @ORM\Column(name="enlistingTimestamp", type="datetime", nullable=true)
     */
    private $enlistingTimestamp;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $confirmationUser;

    /**
     * @ORM\Column(name="confirmationTimestamp", type="datetime", nullable=true)
     */
    private $confirmationTimestamp;

    public function __construct(Person $person = null, Training $training = null, $ip = null)
    {
        $this->person = $person;
        $this->training = $training;
        $this->ip = $ip;
        if ($person !== null && $training !== null) {
            $this->enlistingTimestamp = new \DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTraining(Training $training)
    {
        $this->training = $training;

        return $this;
    }

    public function getTraining()
    {
        return $this->training;
    }

    public function setEnlistingIp($enlistingIp)
    {
        $this->enlistingIp = $enlistingIp;

        return $this;
    }

    public function getEnlistingIp()
    {
        return $this->enlistingIp;
    }

    public function setEnlistingTimestamp(\DateTime $timestamp)
    {
        $this->enlistingTimestamp = $timestamp;

        return $this;
    }

    public function getEnlistingTimestamp()
    {
        return $this->enlistingTimestamp;
    }

    public function setPerson(Person $person) : Attendance
    {
        $this->person = $person;

        return $this;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function setConfirmationUser(?User $confirmationUser) : Attendance
    {
        $this->confirmationUser = $confirmationUser;

        return $this;
    }

    public function getConfirmationUser()
    {
        return $this->confirmationUser;
    }

    public function setConfirmationTimestamp(?\DateTime $confirmationTimestamp) : Attendance
    {
        $this->confirmationTimestamp = $confirmationTimestamp;

        return $this;
    }

    public function getConfirmationTimestamp()
    {
        return $this->confirmationTimestamp;
    }
}
