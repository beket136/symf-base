<?php

namespace Tests\Common;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommonTest extends WebTestCase
{

    private $dbConnection;

    public function testDbConnectionIndex()
    {
        $kernel = self::bootKernel();
        $this->dbConnection = $kernel->getContainer()
            ->get('doctrine')
            ->getConnection();
        var_dump($this->dbConnection);
        $this->assertContains(true, [$this->dbConnection->isConnected()]);
    }
}
