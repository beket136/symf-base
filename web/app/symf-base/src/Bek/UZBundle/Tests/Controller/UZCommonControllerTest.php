<?php

namespace Bek\UZBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UZCommonControllerTest extends WebTestCase
{
    public function testInfo()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/info');
    }

}
