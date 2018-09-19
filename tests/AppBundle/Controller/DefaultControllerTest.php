<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $connectedClient;
    
    public function setUp()
    {
        $this->connectedClient = self::createClient();
        $crawler = $this->connectedClient->request('GET', 'login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues(array(
            '_username' => 'user_1',
            '_password'   => 'user_1',
        ));
        $this->connectedClient->submit($form);
    }  
    
    public function testIndex()
    {
        $this->connectedClient->request('GET', '/');

        $this->assertTrue($this->connectedClient->getResponse()->isSuccessful());
    }
}
