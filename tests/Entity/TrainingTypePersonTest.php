<?php

namespace App\Tests\Entity;

use App\Entity\Person;
use App\Entity\TrainingType;
use App\Entity\TrainingTypePerson;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TrainingTypePersonTest extends TestCase
{
    protected static $testObject;

    public static function setUpBeforeClass() : void
    {
        self::$testObject = new TrainingTypePerson();
    }

    public function testGetId()
    {
        $this->assertSame(null, self::$testObject->getId());
    }

    public function testSetPerson()
    {
        $person = new Person();
        $this->assertSame(self::$testObject, self::$testObject->setPerson($person));
        return $person;
    }

    /**
     * @depends testSetPerson
     */
    public function testGetPerson(Person $person)
    {
        $this->assertSame($person, self::$testObject->getPerson());
    }

    public function testSetTrainingType()
    {
        $trainingType = new TrainingType();
        $this->assertSame(self::$testObject, self::$testObject->setTrainingType($trainingType));
        return $trainingType;
    }

    /**
     * @depends testSetTrainingType
     */
    public function testGetTrainingType(TrainingType $trainingType)
    {
        $this->assertSame($trainingType, self::$testObject->getTrainingType());
        self::$testObject->setTrainingType(null);
        $this->assertSame(null, self::$testObject->getTrainingType());
    }

    public function testSetRole()
    {
        $role = 'ADMIN';
        $this->assertSame(self::$testObject, self::$testObject->setRole($role));
        return $role;
    }

    /**
     * @depends testSetRole
     */
    public function testGetRole(string $role)
    {
        $this->assertSame($role, self::$testObject->getRole());
    }

    public function testSetActiveSince()
    {
        $now = new \DateTime();
        $this->assertSame(self::$testObject, self::$testObject->setActiveSince($now));
        return $now;
    }

    /**
     * @depends testSetActiveSince
     */
    public function testGetActiveSince(\DateTime $now)
    {
        $this->assertSame($now, self::$testObject->getActiveSince());
    }

    public function testSetActiveUntil()
    {
        $now = new \DateTime();
        $this->assertSame(self::$testObject, self::$testObject->setActiveUntil($now));
        return $now;
    }

    /**
     * @depends testSetActiveUntil
     */
    public function testGetActiveUntil(\DateTime $now)
    {
        $this->assertSame($now, self::$testObject->getActiveUntil());
    }

    /**
     * @depends testSetPerson
     */
    public function test__ToString(Person $person)
    {
        $this->assertSame($person->__toString(), self::$testObject->__toString());
    }
}
