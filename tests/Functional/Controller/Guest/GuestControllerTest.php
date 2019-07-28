<?php

namespace App\Tests\Functional\Controller\Guest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GuestControllerTest extends WebTestCase
{
    public function testController()
    {
        $client = static::createClient();

        $client->request('GET', '/guest');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}