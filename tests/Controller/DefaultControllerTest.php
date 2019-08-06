<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testDefaultIndex()
    {
        $client = self::createClient();
        $client->request('GET', '/');

        $this->assertEquals(true, $client->getResponse()->isRedirection());
    }
}
