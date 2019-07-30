<?php

namespace App\Tests\Entity;

use App\Entity\Attendance;
use App\Entity\Training;
use App\Entity\Person;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class AttendanceTest extends TestCase
{
    protected static $testObject;

    public static function setUpBeforeClass() : void
    {
        self::$testObject = new Attendance();
    }

    public function test__Construct()
    {
        $person = new Person();
        $training = new Training();
        $newObject = new Attendance($person, $training);
        $this->assertInstanceOf(\DateTime::class, $newObject->getEnlistingTimestamp());
    }

    public function testGetId()
    {
        $this->assertSame(null, self::$testObject->getId());
    }

    public function testSetTraining()
    {
        $training = new Training();
        $this->assertSame(self::$testObject, self::$testObject->setTraining($training));
        return $training;
    }

    /**
     * @depends testSetTraining
     */
    public function testGetTraining(Training $training)
    {
        $this->assertSame($training, self::$testObject->getTraining());
    }

    public function testSetEnlistingIp()
    {
        $ip = '1.2.3.4';
        $this->assertSame(self::$testObject, self::$testObject->setEnlistingIp($ip));
        return $ip;
    }

    /**
     * @depends testSetEnlistingIp
     */
    public function testGetEnlistingIp(string $ip)
    {
        $this->assertSame($ip, self::$testObject->getEnlistingIp());
    }

    public function testSetEnlistingTimestamp()
    {
        $now = new \DateTime();
        $this->assertSame(self::$testObject, self::$testObject->setEnlistingTimestamp($now));
        return $now;
    }

    /**
     * @depends testSetEnlistingTimestamp
     */
    public function testGetEnlistingTimestamp(\DateTime $now)
    {
        $this->assertSame($now, self::$testObject->getEnlistingTimestamp());
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

    public function testSetConfirmationUser()
    {
        $this->assertSame(self::$testObject, self::$testObject->setConfirmationUser(null));
        $user = new User();
        self::$testObject->setConfirmationUser($user);
        return $user;
    }

    /**
     * @depends testSetConfirmationUser
     */
    public function testGetConfirmationUser(User $user)
    {
        $this->assertSame($user, self::$testObject->getConfirmationUser());
    }

    public function testSetConfirmationTimestamp()
    {
        $this->assertSame(self::$testObject, self::$testObject->setConfirmationTimestamp(null));
        $now = new \DateTime();
        self::$testObject->setConfirmationTimestamp($now);
        return $now;
    }

    /**
     * @depends testSetConfirmationTimestamp
     */
    public function testGetConfirmationTimestamp(\DateTime $now)
    {
        $this->assertSame($now, self::$testObject->getConfirmationTimestamp());
    }
}
