<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author mrosser
 * @ORM\Entity()
 */
class Training {
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"public"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="TrainingType", inversedBy="trainings")
     * @Groups({"public"})
     */
    private $trainingType;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"public"})
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"public"})
     */
    private $place;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $public;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Groups({"public"})
     */
    private $description;

    /**
     * @ORM\Column(name="start", type="datetime", nullable=true)
     * @Groups({"public"})
     */
    private $start;

    /**
     * @ORM\Column(name="end", type="datetime", nullable=true)
     * @Groups({"public"})
     */
    private $end;

    /**
     * @ORM\OneToMany(targetEntity="Attendance", mappedBy="training")
     */
    private $attendances;

    public function __construct()
    {
        $this->attendances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCreator(User $creator) : Training
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function getTrainingType()
    {
        return $this->trainingType;
    }

    public function setTrainingType(TrainingType $trainingType)
    {
        $this->trainingType = $trainingType;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setPlace(string $place)
    {
        $this->place = $place;

        return $this;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setStart(\DateTime $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setEnd(\DateTime $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setAttendances($attendances)
    {
        foreach ($attendances as $attendance) {
            if (!$attendance instanceof Attendance) {
                throw new \InvalidArgumentException('Variable is not of type Attendance: '.$attendance);
            }
            $this->attendances->add($attendance);
        }
        return $this;
    }

    public function getAttendances()
    {
        return $this->attendances;
    }

    public function getEnlistedAttendances()
    {
        return $this->attendances->filter(
            function ($attendance) {
                return $attendance->getEnlistingTimestamp() !== null &&
                    $attendance->getConfirmationUser() === null ;
            }
        );
    }

    public function getConfirmedAttendances()
    {
        return $this->attendances->filter(
            function ($attendance) {
                return $attendance->getConfirmationUser() !== null;
            }
        );
    }

    public function getAttendanceForPerson(Person $person)
    {
        $attendance = $this->attendances->filter(
            function ($attendance) use ($person) {
                return $attendance->getPerson() === $person;
            }
        );

        if (count($attendance) > 0) {
            $attendance = $attendance->first();
        } else {
            $attendance = null;
        }

        return $attendance;
    }

    public function getPublic()
    {
        return $this->public;
    }

    public function setPublic(?string $public)
    {
        $this->public = $public;

        return $this;
    }

    public function __toString()
    {
        $displayString = '';
        if ($this->title !== null) {
            $displayString .= $this->title.', ';
        }
        if ($this->start->diff($this->end)->days === 0) {
            $displayString .= $this->start->format('d.m.Y, H:i').' - '.$this->end->format('H:i');
        } else {
            $displayString .= $this->start->format('d.m.Y H:i').' - '.$this->end->format('d.m.Y H:i');
        }
        return $displayString;
    }
}
