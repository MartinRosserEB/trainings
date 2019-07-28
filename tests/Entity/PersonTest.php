<?php

namespace App\Tests\Entity;

use App\Entity\Person;
use App\Entity\User;
use App\Entity\TrainingTypePerson;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    protected static $testObject;

    public static function setUpBeforeClass() : void
    {
        self::$testObject = new Person();
    }

    public function test__Construct()
    {
        $newObj = new Person();
        $this->assertInstanceOf(ArrayCollection::class, $newObj->getPersonTrainingTypes());
    }
    public function testGetId()
    {
        $this->assertSame(null, self::$testObject->getId());
    }

    public function testSetFirstName()
    {
        $firstName = 'John';
        $this->assertSame(self::$testObject, self::$testObject->setFirstName($firstName));
        return $firstName;
    }

    /**
     * @depends testSetFirstName
     */
    public function testGetFirstName(string $firstName)
    {
        $this->assertSame($firstName, self::$testObject->getFirstName());
    }

    /**
     * @depends testSetFirstName
     */
    public function testSetFamilyName()
    {
        $familyName = 'Doe';
        $this->assertSame(self::$testObject, self::$testObject->setFamilyName($familyName));
        return $familyName;
    }

    /**
     * @depends testSetFamilyName
     */
    public function testGetFamilyName(string $familyName)
    {
        $this->assertSame($familyName, self::$testObject->getFamilyName());
        return self::$testObject->getFirstName().' '.$familyName;
    }

    public function testSetBirthdate()
    {
        $birthdate = new \DateTime();
        $this->assertSame(self::$testObject, self::$testObject->setBirthdate($birthdate));
        return $birthdate;
    }

    /**
     * @depends testSetBirthdate
     */
    public function testGetBirthdate(\DateTime $birthdate)
    {
        $this->assertSame($birthdate, self::$testObject->getBirthdate());
    }

    public function testSetStreet()
    {
        $street = 'Example Avenue';
        $this->assertSame(self::$testObject, self::$testObject->setStreet($street));
        return $street;
    }

    /**
     * @depends testSetStreet
     */
    public function testGetStreet(string $street)
    {
        $this->assertSame($street, self::$testObject->getStreet());
    }

    public function testSetStreetNo()
    {
        $streetNo = '21';
        $this->assertSame(self::$testObject, self::$testObject->setStreetNo($streetNo));
        return $streetNo;
    }

    /**
     * @depends testSetStreetNo
     */
    public function testGetStreetNo(string $streetNo)
    {
        $this->assertSame($streetNo, self::$testObject->getStreetNo());
    }

    public function testSetCity()
    {
        $city = 'New Berne';
        $this->assertSame(self::$testObject, self::$testObject->setCity($city));
        return $city;
    }

    /**
     * @depends testSetCity
     */
    public function testGetCity(string $city)
    {
        $this->assertSame($city, self::$testObject->getCity());
    }

    public function testSetZipCode()
    {
        $zipCode = 'NB 3003';
        $this->assertSame(self::$testObject, self::$testObject->setZipCode($zipCode));
        return $zipCode;
    }

    /**
     * @depends testSetZipCode
     */
    public function testGetZipCode(string $zipCode)
    {
        $this->assertSame($zipCode, self::$testObject->getZipCode());
    }

    public function testSetPhone()
    {
        $phone = '+12 34 567 89 01';
        $this->assertSame(self::$testObject, self::$testObject->setPhone($phone));
        return $phone;
    }

    /**
     * @depends testSetPhone
     */
    public function testGetPhone(string $phone)
    {
        $this->assertSame($phone, self::$testObject->getPhone());
    }

    public function testSetUser()
    {
        $user = new User();
        $this->assertSame(self::$testObject, self::$testObject->setUser(null));
        self::$testObject->setUser($user);
        return $user;
    }

    /**
     * @depends testSetUser
     */
    public function testGetUser(User $user)
    {
        $this->assertSame($user, self::$testObject->getUser());
    }

    public function testAddPersonTrainingType()
    {
        $ptp = new TrainingTypePerson();
        $this->assertSame(self::$testObject, self::$testObject->addPersonTrainingType($ptp));
        self::$testObject->addPersonTrainingType(new TrainingTypePerson());
        return $ptp;
    }

    /**
     * @depends testAddPersonTrainingType
     */
    public function testGetPersonTrainingTypes(TrainingTypePerson $ptp)
    {
        $ptps = self::$testObject->getPersonTrainingTypes();
        $this->assertEquals(2, count($ptps));
        $this->assertSame($ptp, $ptps->first());
        $this->assertInstanceOf(TrainingTypePerson::class, $ptps->last());
        return $ptp;
    }

    /**
     * @depends testGetPersonTrainingTypes
     */
    public function testRemovePersonTrainingType(TrainingTypePerson $ptp)
    {
        $this->assertSame(self::$testObject, self::$testObject->removePersonTrainingType(new TrainingTypePerson()));
        $this->assertEquals(2, count(self::$testObject->getPersonTrainingTypes()));
        self::$testObject->removePersonTrainingType($ptp);
        $this->assertEquals(1, count(self::$testObject->getPersonTrainingTypes()));
    }

    /**
     * @depends testGetFamilyName
     */
    public function test__ToString(string $fullName)
    {
        $this->assertSame($fullName, self::$testObject->__toString());
    }
}
