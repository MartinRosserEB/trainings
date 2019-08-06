<?php

namespace App\Tests\Controller;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testShowByHash()
    {
        $client = self::createClient();
        $hash = 'ac37a138bb7fd07d622ba45df045f144';
        $client->request('GET', '/public/training/show/'.$hash);

        $this->assertEquals(true, $client->getResponse()->isSuccessful());
    }
}
