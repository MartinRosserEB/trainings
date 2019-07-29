<?php

namespace App\Tests\Entity;

use App\Entity\Person;
use App\Entity\TrainingType;
use App\Entity\TrainingTypePerson;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TrainingTypeTest extends TestCase
{
    protected static $testObject;

    public static function setUpBeforeClass() : void
    {
        self::$testObject = new TrainingType();
    }

    public function test__Construct()
    {
        $newObj = new TrainingType();
        $this->assertInstanceOf(ArrayCollection::class, $newObj->getTrainingTypePersons());
        $this->assertInstanceOf(ArrayCollection::class, $newObj->getTrainings());
    }

    public function testGetId()
    {
        $this->assertSame(null, self::$testObject->getId());
    }

    public function testSetName()
    {
        $name = 'Example name';
        $this->assertSame(self::$testObject, self::$testObject->setName($name));
        return $name;
    }

    /**
     * @depends testSetName
     */
    public function testGetName(string $name)
    {
        $this->assertSame($name, self::$testObject->getName());
    }

    public function testSetDescription()
    {
        $description = 'Example description';
        $this->assertSame(self::$testObject, self::$testObject->setDescription($description));
        return $description;
    }

    /**
     * @depends testSetDescription
     */
    public function testGetDescription(string $description)
    {
        $this->assertSame($description, self::$testObject->getDescription());
    }

    public function testAddTrainingTypePerson()
    {
        $testArray = [
            'activeCount' => 2,
            'ttps' => [],
            'users' => [],
        ];

        for ($i = 0; $i < 4; $i++) {
            $tmpUser = new User();
            $tmpPerson = new Person();
            $tmpPerson->setUser($tmpUser);
            $tmpTTP = new TrainingTypePerson();
            $tmpTTP->setPerson($tmpPerson);
            if ($i % 2 === 0) {
                $tmpTTP->setActiveUntil(new \DateTime());
            }
            $testArray['ttps'][] = $tmpTTP;
            $testArray['users'][] = $tmpUser;
            $this->assertSame(self::$testObject, self::$testObject->addTrainingTypePerson($tmpTTP));
        }
        return $testArray;
    }

    /**
     * @depends testAddTrainingTypePerson
     */
    public function testGetActiveTrainingTypePersons(array $testArray)
    {
        $this->assertEquals($testArray['activeCount'], count(self::$testObject->getActiveTrainingTypePersons()));
    }

    /**
     * @depends testAddTrainingTypePerson
     */
    public function testGetActiveTrainingTypePersonFor(array $testArray)
    {
        $this->assertEquals(null, self::$testObject->getActiveTrainingTypePersonFor(new User()));
        $this->assertInstanceOf(TrainingTypePerson::class, self::$testObject->getActiveTrainingTypePersonFor($testArray['users'][1]));
    }

    /**
     * @depends testAddTrainingTypePerson
     */
    public function testRemoveTrainingTypePerson(array $testArray)
    {
        $this->assertEquals($testArray['ttps'], self::$testObject->getTrainingTypePersons()->toArray());
        self::$testObject->removeTrainingTypePerson($testArray['ttps'][1]);
        $this->assertEquals($testArray['activeCount'] - 1, count(self::$testObject->getActiveTrainingTypePersons()));
    }

    /**
     * @depends testSetName
     */
    public function test__ToString(string $name)
    {
        $this->assertSame($name, self::$testObject->__toString());
    }
}
