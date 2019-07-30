<?php

namespace App\Tests\Entity;

use App\Entity\Attendance;
use App\Entity\Person;
use App\Entity\Training;
use App\Entity\TrainingType;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TrainingTest extends TestCase
{
    protected static $testObject;

    public static function setUpBeforeClass() : void
    {
        self::$testObject = new Training();
    }

    public function test__Construct()
    {
        $newObj = new Training();
        $this->assertInstanceOf(ArrayCollection::class, $newObj->getAttendances());
    }

    public function testGetId()
    {
        $this->assertSame(null, self::$testObject->getId());
    }

    public function testSetCreator()
    {
        $creator = new User();
        $this->assertSame(self::$testObject, self::$testObject->setCreator($creator));
        return $creator;
    }

    /**
     * @depends testSetCreator
     */
    public function testGetCreator(User $creator)
    {
        $this->assertSame($creator, self::$testObject->getCreator());
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
    }

    public function testSetTitle()
    {
        $title = 'Example training';
        $this->assertSame(self::$testObject, self::$testObject->setTitle($title));
        return $title;
    }

    /**
     * @depends testSetTitle
     */
    public function testGetTitle(string $title)
    {
        $this->assertSame($title, self::$testObject->getTitle());
        return $title;
    }

    public function testSetPlace()
    {
        $place = 'Example place';
        $this->assertSame(self::$testObject, self::$testObject->setPlace($place));
        return $place;
    }

    /**
     * @depends testSetPlace
     */
    public function testGetPlace(string $place)
    {
        $this->assertSame($place, self::$testObject->getPlace());
    }

    public function testSetDescription()
    {
        $description = 'example description';
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

    /**
     * @depends testGetTitle
     */
    public function testSetStart(string $title)
    {
        $start = new \DateTime();
        $this->assertSame(self::$testObject, self::$testObject->setStart($start));
        return [
            'title' => $title,
            'start' => $start
        ];
    }

    /**
     * @depends testSetStart
     */
    public function testGetStart(Array $titleStart)
    {
        $this->assertSame($titleStart['start'], self::$testObject->getStart());
        return $titleStart;
    }

    /**
     * @depends testGetStart
     */
    public function testSetEnd(Array $titleStart)
    {
        $end = new \DateTime();
        $this->assertSame(self::$testObject, self::$testObject->setEnd($end));
        $titleStart['end'] = $end;
        return $titleStart;
    }

    /**
     * @depends testSetEnd
     */
    public function testGetEnd(Array $titleStartEnd)
    {
        $this->assertSame($titleStartEnd['end'], self::$testObject->getEnd());
        return $titleStartEnd;
    }

    public function testSetAttendancesException()
    {
        $this->expectException(\InvalidArgumentException::class);
        self::$testObject->setAttendances(['a']);
    }

    public function testSetAttendances()
    {
        $testArray = [
            'attendances' => [],
            'enlistedCount' => 2,
            'confirmedCount' => 2,
            'person' => new Person(),
        ];

        for ($i = 0; $i < 4; $i++) {
            $tmpAttendance = new Attendance();
            $tmpAttendance->setEnlistingIp('1.2.3.4');
            $tmpAttendance->setEnlistingTimestamp(new \DateTime());
            $tmpAttendance->setTraining(self::$testObject);
            $testArray['attendances'][] = $tmpAttendance;
        }
        $testArray['attendances'][0]->setPerson($testArray['person']);
        $tmpUser = new User();
        for ($i = 2; $i < 4; $i++) {
            $testArray['attendances'][$i]->setConfirmationUser($tmpUser);
            $testArray['attendances'][$i]->setConfirmationTimestamp(new \DateTime());
        }

        $this->assertSame(self::$testObject, self::$testObject->setAttendances($testArray['attendances']));
        return $testArray;
    }

    /**
     * @depends testSetAttendances
     */
    public function testGetAttendances(array $testArray)
    {
        $this->assertSame($testArray['attendances'], self::$testObject->getAttendances()->toArray());
    }

    /**
     * @depends testSetAttendances
     */
    public function testGetEnlistedAttendances(array $testArray)
    {
        $this->assertEquals($testArray['enlistedCount'], count(self::$testObject->getEnlistedAttendances()));
    }

    /**
     * @depends testSetAttendances
     */
    public function testGetConfirmedAttendances(array $testArray)
    {
        $this->assertEquals($testArray['confirmedCount'], count(self::$testObject->getConfirmedAttendances()));
    }

    /**
     * @depends testSetAttendances
     */
    public function testGetAttendanceForPerson(array $testArray)
    {
        $attendance = self::$testObject->getAttendanceForPerson($testArray['person']);
        $this->assertInstanceOf(Attendance::class, $attendance);
        $this->assertSame($testArray['person'], $attendance->getPerson());
        $this->assertSame(null, self::$testObject->getAttendanceForPerson(new Person()));        
    }

    public function testSetPublic()
    {
        self::$testObject->setPublic(false);
        $this->assertSame(self::$testObject, self::$testObject->setPublic(true));
    }

    /**
     * @depends testSetPublic
     */
    public function testGetPublic()
    {
        self::$testObject->setPublic(false);
        $this->assertEquals(null, self::$testObject->getPublic());
        self::$testObject->setPublic(true);
        $this->assertNotEquals(null, self::$testObject->getPublic());
    }

    /**
     * @depends testGetEnd
     */
    public function test__ToString(Array $titleStartEnd)
    {
        $dateTimeFormat = 'd.m.Y, H:i';
        $dateTimeShort = 'H:i';
        $stringRepresentation = $titleStartEnd['title'].', '.$titleStartEnd['start']->format($dateTimeFormat).' - '.$titleStartEnd['end']->format($dateTimeShort);
        $this->assertSame($stringRepresentation, self::$testObject->__toString());
        $start = \DateTime::createFromFormat($dateTimeFormat, '01.01.2020, 12:05');
        $end = \DateTime::createFromFormat($dateTimeFormat, '03.01.2020, 10:27');
        self::$testObject->setStart($start);
        self::$testObject->setEnd($end);
        $dateTimeFormatLong = 'd.m.Y H:i';
        $stringRepresentation = $titleStartEnd['title'].', '.$start->format($dateTimeFormatLong).' - '.$end->format($dateTimeFormatLong);
        $this->assertSame($stringRepresentation, self::$testObject->__toString());
    }
}
