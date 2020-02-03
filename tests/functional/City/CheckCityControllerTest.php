<?php

namespace App\Tests\functional\City;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckCityControllerTest extends WebTestCase
{
    public function testValidCity()
    {
        $client = static::createClient();

        $client->request('GET', '/check?city=Berlin');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testInvalidCity()
    {
        $client = static::createClient();

        $client->request('GET', '/check?city=Madrid');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testEmptyCityParameter()
    {
        $client = static::createClient();

        $client->request('GET', '/check?city=');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}
