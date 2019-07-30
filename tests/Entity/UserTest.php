<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Person;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected static $testObject;

    public static function setUpBeforeClass() : void
    {
        self::$testObject = new User();
    }

    public function test__Construct()
    {
        $user = new User();
        $this->assertTrue(is_array($user->getRoles()));
        $this->assertInstanceOf(ArrayCollection::class, $user->getPersons());
    }

    public function testGetId()
    {
        $this->assertSame(null, self::$testObject->getId());
    }

    public function testSetEmail()
    {
        $this->assertSame(self::$testObject, self::$testObject->setEmail('test@example.com'));
    }

    /**
     * @depends testSetEmail
     */
    public function testGetEmail()
    {
        $this->assertEquals('test@example.com', self::$testObject->getEmail());
    }

    /**
     * @depends testSetEmail
     */
    public function testGetUsername()
    {
        $this->assertEquals('test@example.com', self::$testObject->getUsername());
    }

    /**
     * @depends testSetEmail
     */
    public function test__ToString()
    {
        $this->assertEquals('test@example.com', self::$testObject->__toString());
    }

    public function testAddRole()
    {
        $this->assertSame(self::$testObject, self::$testObject->addRole('ROLE_USER'));
        self::$testObject->addRole('ROLE_ADMIN');
        self::$testObject->addRole('ROLE_USER');
        self::$testObject->addRole('ROLE_ADMIN');
    }

    /**
     * @depends testAddRole
     */
    public function testGetRoles()
    {
        $roles = self::$testObject->getRoles();
        $this->assertSame($roles, [
            'ROLE_USER',
            'ROLE_ADMIN',
        ]);
    }

    /**
     * @depends testGetRoles
     */
    public function testRemoveRole()
    {
        $this->assertSame(self::$testObject, self::$testObject->removeRole('ROLE_ADMIN'));
        self::$testObject->removeRole('ROLE_INEXISTENT');
        $roles = self::$testObject->getRoles();
        $this->assertSame($roles, ['ROLE_USER']);
    }

    public function testAddPerson()
    {
        $p1 = $this->createMock(Person::class);

        $p1->expects($this->once())
            ->method('setUser')
            ->with($this->equalTo(self::$testObject));

        $this->assertSame(self::$testObject, self::$testObject->addPerson($p1));
    }

    /**
     * @depends testAddPerson
     */
    public function testGetPersons()
    {
        $this->assertInstanceOf(Person::class, self::$testObject->getPersons()->first());
    }

    /**
     * @depends testAddPerson
     */
    public function testRemovePerson()
    {
        self::$testObject->removePerson(new Person());
        $persons = self::$testObject->getPersons();

        $persons->first()->expects($this->once())
            ->method('setUser')
            ->with($this->equalTo(null));

        $this->assertEquals(1, count($persons));
        $this->assertSame(self::$testObject, self::$testObject->removePerson($persons->first()));
        $this->assertEquals(0, count($persons));
    }

    public function testSetPassword()
    {
        $this->assertSame(self::$testObject, self::$testObject->setPassword('testpassword'));
    }

    /**
     * @depends testSetPassword
     */
    public function testGetPassword()
    {
        $this->assertSame('testpassword', self::$testObject->getPassword());
    }

    public function testSetApiToken()
    {
        $this->assertSame(self::$testObject, self::$testObject->setApiToken('testapitoken'));
    }

    /**
     * @depends testSetApiToken
     */
    public function testGetApiToken()
    {
        $this->assertSame('testapitoken', self::$testObject->getApiToken());
    }

    public function testGetSalt()
    {
        $this->assertSame(null, self::$testObject->getSalt());
    }

    public function testEraseCredentials()
    {
        $this->assertSame(null, self::$testObject->eraseCredentials());
    }
}
