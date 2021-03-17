<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EndPointTest extends WebTestCase
{
    /**
     * @dataProvider urlOkList
     */
    public function testOk($url)
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function urlOkList()
    {
        yield ['/api/home_events/10'];
        yield ['/api/home_event/10'];
        yield ['/api/home_types'];
        yield ['/api/home_places'];
    }
}
