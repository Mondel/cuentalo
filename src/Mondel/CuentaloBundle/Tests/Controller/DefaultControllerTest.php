<?php
namespace Mondel\CuentaloBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Status 200 en portada");
        
        $this->assertTrue($crawler->filter('h3')->count() == 3);
        $this->assertTrue($crawler->filter('a')->count() == 31);
    }
    
}
